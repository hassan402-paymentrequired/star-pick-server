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
            'fixture_id' => 'required|exists:fixtures,id',
            'matches' => 'required|array',
            'matches.*.playerId' => 'required|exists:players,id',
            'matches.*.againstTeam' => 'required|exists:teams,external_id',
        ]);

        $fixture = Fixture::find($request->fixture_id);

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
                'is_completed' => false,
            ]);
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
