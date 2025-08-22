<?php

namespace App\Console\Commands\ApiFootballCommand;

use App\Models\League;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FetchTeams extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:teams {league} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch teams for a given league and season from the API and store them in the teams table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $leagueSeason = League::with('seasons')
            ->whereHas('seasons', function ($query) {
                $query->where('is_current', true);
            })
            ->first();
        Log::info('Fetching teams for league: ' . json_encode($leagueSeason->toArray()));
        $leagueId = $this->argument('league');
        $season = '2023';
        $apiUrl = 'https://v3.football.api-sports.io/teams';
        $apiKey = env('SPORT_API_KEY');
        $page = 1;
        $totalPages = 1;
        $insertBatch = [];

        do {
            $this->info("Fetching teams for league $leagueId, season $season, page $page...");
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'x-rapidapi-key' => $apiKey
            ])->get($apiUrl, [
                'league' => $leagueId,
                'season' => $season,
            ]);

            if (!$response->ok()) {
                $this->error('Failed to fetch teams: ' . $response->body());
                return 1;
            }

            $body = $response->json();

            // $this->info(json_encode($body));

            $teams = $body['response'] ?? [];
            $paging = $body['paging'] ?? ['current' => $page, 'total' => $page];
            $totalPages = $paging['total'] ?? 1;

            foreach ($teams as $item) {
                $team = $item['team'];
                $insertBatch[] = [
                    'external_id' => $team['id'],
                    'name'        => $team['name'],
                    'code'        => $team['code'] ?? '',
                    'country'     => $team['country'] ?? '',
                    'logo'        => $team['logo'] ?? '',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            }

            if (count($insertBatch) >= 500) {
                \App\Models\Team::upsert($insertBatch, ['external_id'], ['name', 'code', 'country', 'logo', 'status', 'updated_at']);
                $insertBatch = [];
            }

            $page++;
        } while ($page <= $totalPages);

        if (!empty($insertBatch)) {
            \App\Models\Team::upsert($insertBatch, ['external_id'], ['name', 'code', 'country', 'logo', 'status', 'updated_at']);
        }

        $this->info('All teams fetched and inserted/updated successfully.');
    }
}
