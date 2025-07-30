<?php

namespace App\Http\Controllers\V1\Match;

use App\Http\Controllers\Controller;
use App\Utils\Service\V1\Match\MatchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use App\Models\PlayerMatch;
use App\Models\Fixture;
use App\Models\Player;
use App\Models\Team;
use App\Models\PlayerStatistic;

class MatchController extends Controller
{
    private MatchService $matchService;

    public function __construct(MatchService $matchService)
    {
        $this->matchService = $matchService;
    }

    public function index(): JsonResponse
    {
        $matches =  $this->matchService->matches();
        return $this->respondWithCustomData([
            'matches' => $matches[0],
            'team' => $matches[1],
            'leagues' => $matches[2],
        ], 200);
    }

    public function refetchStatistics(Request $request)
    {
        $league = $request->league;
        $season = $request->season;
        $from = $request->from;
        $to = $request->to;

        Artisan::call('fetch:weekly-fixtures', ['league' => $league, 'season' => $season, 'from' => $from, 'to' => $to]);
        return $this->respondWithCustomData([
            'message' => 'Statistics refetched successfully'
        ], 200);
    }

    /**
     * Create matches from fixtures (admin only)
     * Request body: [{playerId: 20, againstTeam: 40}, {playerId: 3, againstTeam: 49}, ...]
     */
    public function createFromFixture(Request $request)
    {
        $request->validate([
            'fixture_id' => 'required',
            'matches' => 'required|array',
            'matches.*.playerId' => 'required|exists:players,id',
            'matches.*.againstTeam' => 'required|exists:teams,external_id',
            'league_id' => 'required|exists:leagues,external_id',
            'season_id' => 'required|exists:seasons,external_id',

            'fixture.awayScore' => 'nullable|array',
            'fixture.awayTeam.name' => 'required|string',
            'fixture.awayTeam.slug' => 'required|string',
            'fixture.awayTeam.shortName' => 'required|string',
            'fixture.awayTeam.id' => 'required',
            'fixture.awayTeam.sport' => 'required|array',
            'fixture.changes.changeTimestamp' => 'required|integer',
            'fixture.crowdsourcingDataDisplayEnabled' => 'required|boolean',
            'fixture.customId' => 'required|string',
            'fixture.detailId' => 'required|integer',
            'fixture.feedLocked' => 'required|boolean',
            'fixture.finalResultOnly' => 'required|boolean',
            'fixture.hasGlobalHighlights' => 'required|boolean',
            'fixture.homeScore' => 'nullable|array',
            'fixture.homeTeam.name' => 'required|string',
            'fixture.homeTeam.slug' => 'required|string',
            'fixture.homeTeam.shortName' => 'required|string',
            'fixture.homeTeam.id' => 'required',
            'fixture.homeTeam.sport' => 'required|array',
            'fixture.id' => 'required|integer',
            'fixture.isEditor' => 'required|boolean',
            'fixture.roundInfo.round' => 'required|integer',
            'fixture.season.name' => 'required|string',
            'fixture.season.year' => 'required|string',
            'fixture.season.editor' => 'required|boolean',
            'fixture.season.id' => 'required|integer',
            'fixture.slug' => 'required|string',
            'fixture.startTimestamp' => 'required|integer',
            'fixture.status.code' => 'required|integer',
            'fixture.status.description' => 'required|string',
            'fixture.status.type' => 'required|string',
            'fixture.time' => 'nullable|array',
            'fixture.tournament.name' => 'required|string',
            'fixture.tournament.slug' => 'required|string',
            'fixture.tournament.category' => 'required|array',
            'fixture.tournament.uniqueTournament' => 'required|array',
            'fixture.tournament.priority' => 'required|integer',
            'fixture.varInProgress.homeTeam' => 'required|boolean',
            'fixture.varInProgress.awayTeam' => 'required|boolean',
        ]);

        $fixture = Fixture::updateOrCreate(
            [
                'external_id' => $request->fixture['id'],
            ],
            [
                'league_id' => $request->league_id,
                'season' => $request->season_id,
                'date' => $request->fixture['startTimestamp'] ? date('Y-m-d H:i:s', $request->fixture['startTimestamp'] / 1000) : now()->format('Y-m-d H:i:s'),
                'timestamp' => $request->fixture->startTimestamp ?? now()->timestamp,
                'venue_id' => $venue['id'] ?? null,
                'venue_name' => $venue['name'] ?? null,
                'venue_city' => $venue['city'] ?? null,
                'home_team_id' => $request->fixture['homeTeam']['id'],
                'home_team_name' => $request->fixture['homeTeam']['name'],
                'home_team_logo' => $request->fixture['homeTeam']['name'],
                'away_team_id' => $request->fixture['awayTeam']['id'],
                'away_team_name' => $request->fixture['awayTeam']['name'],
                'away_team_logo' => $request->fixture['awayTeam']['name'],
                'status' => $request->fixture->status['description'] ?? null,
                'goals_home' => $request->fixture['homeScore']['total'] ?? null,
                'goals_away' => $request->fixture['awayScore']['total'] ?? null,
                'raw_json' => json_encode($request->fixture),
            ]
        );


        // Check for duplicate players in the request
        $playerIds = collect($request->matches)->pluck('playerId');
        $duplicatePlayers = $playerIds->duplicates();

        if ($duplicatePlayers->isNotEmpty()) {
            return $this->respondWithCustomData([
                'message' => 'Duplicate players found in request',
                'duplicate_players' => $duplicatePlayers->values()
            ], 422);
        }

        // Check if any players already have matches on the same date
        $existingPlayerMatches = PlayerMatch::whereIn('player_id', $playerIds)
            ->where('date', $fixture->date->format('Y-m-d'))
            ->get();

        if ($existingPlayerMatches->isNotEmpty()) {
            $conflictingPlayers = $existingPlayerMatches->pluck('player_id');
            return $this->respondWithCustomData([
                'message' => 'Some players already have matches on this date',
                'conflicting_players' => $conflictingPlayers->values(),
                'match_date' => $fixture->date->format('Y-m-d')
            ], 422);
        }

        // Check if any players are currently in ongoing fixtures
        $ongoingPlayerMatches = PlayerMatch::whereIn('player_id', $playerIds)
            ->whereHas('fixture', function ($query) {
                $query->whereIn('status', [
                    'First Half',
                    'Second Half',
                    'Halftime',
                    'Extra Time',
                    'Penalty In Progress',
                    'Match Suspended',
                    'Match Interrupted'
                ]);
            })
            ->get();

        if ($ongoingPlayerMatches->isNotEmpty()) {
            $ongoingPlayers = $ongoingPlayerMatches->pluck('player_id');
            return $this->respondWithCustomData([
                'message' => 'Some players are currently in ongoing fixtures',
                'ongoing_players' => $ongoingPlayers->values(),
                'ongoing_matches' => $ongoingPlayerMatches->load(['fixture', 'player'])->map(function ($match) {
                    return [
                        'player_id' => $match->player_id,
                        'player_name' => $match->player->name,
                        'fixture_status' => $match->fixture->status,
                        'fixture_id' => $match->fixture_id
                    ];
                })
            ], 422);
        }

        foreach ($request->matches as $matchData) {
            $team = Team::where('external_id', $matchData['againstTeam'])->firstOrFail();

            $playerMatch = PlayerMatch::create([
                'date' => $fixture->date->format('Y-m-d'),
                'time' => $fixture->date->format('H:i:s'),
                'player_id' => $matchData['playerId'],
                'team_id' => $team->id,
                'fixture_id' => $fixture->id,
                'event_id' => $request->fixture_id,
                'is_completed' => false,
            ]);

            PlayerStatistic::updateOrCreate(
                [
                    'player_id' => $matchData['playerId'],
                    'fixture_id' => $fixture->id,
                ],
                [
                    'match_date' => now(),
                    'team_id' => $team->id,
                    'minutes' => 0,
                    'number' => 0,
                    'rating' => '0',
                    'captain' => 0,
                    'substitute' => 0,
                    'offsides' => 0,
                    'shots_total' => 0,
                    'shots_on' => 0,
                    'goals_total' => 0,
                    'goals_conceded' => 0,
                    'goals_assists' => 0,
                    'goals_saves' => 0,
                    'passes_total' => 0,
                    'passes_key' => 0,
                    'tackles_total' => 0,
                    'tackles_blocks' => 0,
                    'tackles_interceptions' => 0,
                    'duels_total' => 0,
                    'duels_won' => 0,
                    'dribbles_attempts' => 0,
                    'dribbles_success' => 0,
                    'dribbles_past' => 0,
                    'fouls_drawn' => 0,
                    'fouls_committed' => 0,
                    'cards_yellow' => 0,
                    'cards_red' => 0,
                ]
            );
        }

        return $this->respondWithCustomData([
            'message' => 'Matches created successfully',
            'fixture' => $fixture
        ], 201);
    }

    /**
     * Get statistics for a specific player match
     */
    public function getMatchStatistics(PlayerMatch $playerMatch): JsonResponse
    {
        // Get the player statistics for this match's fixture
        $statistics = PlayerStatistic::where('player_id', $playerMatch->player_id)
            ->where('fixture_id', $playerMatch->fixture_id)
            ->first();

        return $this->respondWithCustomData([
            'match' => $playerMatch->load(['player', 'team', 'fixture']),
            'statistics' => $statistics,
            'points' => $statistics ? $statistics->points : 0,
        ], 200);
    }
}
