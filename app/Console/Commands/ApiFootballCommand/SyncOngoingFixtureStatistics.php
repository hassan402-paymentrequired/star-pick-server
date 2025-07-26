<?php

namespace App\Console\Commands\ApiFootballCommand;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Fixture;
use App\Models\PlayerStatistic;
use App\Models\TeamStatistic;

class SyncOngoingFixtureStatistics extends Command
{
    protected $signature = 'sync:ongoing-fixture-statistics';
    protected $description = 'Fetch and update statistics for all ongoing fixtures and sync with the database';

    public function handle()
    {
        $apiUrl = 'https://v3.football.api-sports.io/fixtures';
        $apiKey = env('SPORT_API_KEY');
        $ongoingFixtures = Fixture::whereIn('status', ['First Half', 'Second Half', 'Halftime', 'Extra Time', 'Penalty In Progress', 'Match Suspended', 'Match Interrupted', 'Match Postponed', 'Match Cancelled'])
            ->get();

        if ($ongoingFixtures->isEmpty()) {
            $this->info('No ongoing fixtures found.');
            return 0;
        }

        foreach ($ongoingFixtures as $fixture) {
            $this->info("Syncing statistics for fixture ID: {$fixture->external_id} ({$fixture->home_team_name} vs {$fixture->away_team_name})");
            $response = Http::withHeaders([
                'x-rapidapi-key' => $apiKey
            ])->get($apiUrl, [
                'id' => $fixture->external_id
            ]);

            if (!$response->ok()) {
                $this->error('Failed to fetch statistics: ' . $response->body());
                continue;
            }

            $data = $response->json();
            $apiFixture = $data['response'][0] ?? null;
            if (!$apiFixture) continue;

            // Team statistics
            foreach (($apiFixture['statistics'] ?? []) as $teamStat) {
                $team = $teamStat['team'];
                $stats = collect($teamStat['statistics'])->pluck('value', 'type');
                TeamStatistic ::updateOrCreate(
                    [
                        'fixture_id' => $fixture->id,
                        'team_id' => $team['id'],
                    ],
                    [
                        'shots_on_goal' => $stats['Shots on Goal'] ?? null,
                        'shots_off_goal' => $stats['Shots off Goal'] ?? null,
                        'total_shots' => $stats['Total Shots'] ?? null,
                        'blocked_shots' => $stats['Blocked Shots'] ?? null,
                        'shots_insidebox' => $stats['Shots insidebox'] ?? null,
                        'shots_outsidebox' => $stats['Shots outsidebox'] ?? null,
                        'fouls' => $stats['Fouls'] ?? null,
                        'corner_kicks' => $stats['Corner Kicks'] ?? null,
                        'offsides' => $stats['Offsides'] ?? null,
                        'ball_possession' => $stats['Ball Possession'] ?? null,
                        'yellow_cards' => $stats['Yellow Cards'] ?? null,
                        'red_cards' => $stats['Red Cards'] ?? null,
                        'goalkeeper_saves' => $stats['Goalkeeper Saves'] ?? null,
                        'total_passes' => $stats['Total passes'] ?? null,
                        'passes_accurate' => $stats['Passes accurate'] ?? null,
                        'passes_pct' => $stats['Passes %'] ?? null,
                    ]
                );
            }

            // Player statistics
            foreach (($apiFixture['players'] ?? []) as $teamPlayers) {
                $teamId = $teamPlayers['team']['id'];
                foreach ($teamPlayers['players'] as $playerData) {
                    $player = $playerData['player'];
                    $stats = $playerData['statistics'][0] ?? [];
                    PlayerStatistic::updateOrCreate(
                        [
                            'player_id' => $player['id'],
                            'fixture_id' => $fixture->id,
                        ],
                        [
                            'team_id' => $teamId,
                            'minutes' => $stats['games']['minutes'] ?? null,
                            'number' => $stats['games']['number'] ?? null,
                            'position' => $stats['games']['position'] ?? null,
                            'rating' => $stats['games']['rating'] ?? null,
                            'captain' => $stats['games']['captain'] ?? null,
                            'substitute' => $stats['games']['substitute'] ?? null,
                            'offsides' => $stats['offsides'] ?? null,
                            'shots_total' => $stats['shots']['total'] ?? null,
                            'shots_on' => $stats['shots']['on'] ?? null,
                            'goals_total' => $stats['goals']['total'] ?? null,
                            'goals_conceded' => $stats['goals']['conceded'] ?? null,
                            'goals_assists' => $stats['goals']['assists'] ?? null,
                            'goals_saves' => $stats['goals']['saves'] ?? null,
                            'passes_total' => $stats['passes']['total'] ?? null,
                            'passes_key' => $stats['passes']['key'] ?? null,
                            'passes_accuracy' => $stats['passes']['accuracy'] ?? null,
                            'tackles_total' => $stats['tackles']['total'] ?? null,
                            'tackles_blocks' => $stats['tackles']['blocks'] ?? null,
                            'tackles_interceptions' => $stats['tackles']['interceptions'] ?? null,
                            'duels_total' => $stats['duels']['total'] ?? null,
                            'duels_won' => $stats['duels']['won'] ?? null,
                            'dribbles_attempts' => $stats['dribbles']['attempts'] ?? null,
                            'dribbles_success' => $stats['dribbles']['success'] ?? null,
                            'dribbles_past' => $stats['dribbles']['past'] ?? null,
                            'fouls_drawn' => $stats['fouls']['drawn'] ?? null,
                            'fouls_committed' => $stats['fouls']['committed'] ?? null,
                            'cards_yellow' => $stats['cards']['yellow'] ?? null,
                            'cards_red' => $stats['cards']['red'] ?? null,
                        ]
                    );
                }
            }
        }

        $this->info('All ongoing fixture statistics synced.');
        return 0;
    }
}
