<?php

namespace App\Console\Commands\ApiFootballCommand;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FetchPlayers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:players {league}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch all players for a given league and season from the API and store them in the players table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $leagueId = $this->argument('league');
        $season = 2023;
        $apiUrl = 'https://v3.football.api-sports.io/players';
        $apiKey = env('SPORT_API_KEY');
        $page = 1;
        $insertBatch = [];

        while (true) {
            $this->info("Fetching players for league $leagueId, season $season, page $page...");
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'x-rapidapi-key' => $apiKey
            ])->get($apiUrl, [
                'league' => $leagueId,
                'season' => $season,
                'page' => $page
            ]);

            if (!$response->ok()) {
                $this->error('Failed to fetch players: ' . $response->body());
                return 1;
            }

            $body = $response->json();
            Log::info('Fetch Players Response', $body);
            $players = $body['response'] ?? [];
            $paging = $body['paging'] ?? ['current' => $page, 'total' => $page];
            $currentPage = $paging['current'] ?? $page;
            $totalPages = $paging['total'] ?? $page;

            $this->info("Total players fetched: " . count($players));

            foreach ($players as $item) {
                $player = $item['player'];
                $stats = $item['statistics'][0] ?? [];
                $team = $stats['team'] ?? [];
                $games = $stats['games'] ?? [];
                $position = $games['position'] ?? '';
                $insertBatch[] = [
                    'external_id'  => $player['id'],
                    'name'         => $player['name'],
                    'team_id'      => $team['id'] ?? '',
                    'position'     => $position,
                    'image'        => $player['photo'] ?? '',
                    'nationality'  => $player['nationality'] ?? '',
                    'player_rating' => 1,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            }

            if (count($insertBatch) >= 500) {
                \App\Models\Player::upsert($insertBatch, ['external_id'], ['name', 'team_id', 'position', 'image', 'nationality', 'player_rating', 'updated_at']);
                $insertBatch = [];
            }

            if ($currentPage >= $totalPages) {
                break;
            }
            $page++;
        }

        if (!empty($insertBatch)) {
            \App\Models\Player::upsert($insertBatch, ['external_id'], ['name', 'team_id', 'position', 'image', 'nationality', 'player_rating', 'updated_at']);
        }

        $this->info('All players fetched and inserted/updated successfully.');
    }
}
