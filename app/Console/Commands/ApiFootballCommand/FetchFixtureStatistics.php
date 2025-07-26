<?php

namespace App\Console\Commands\ApiFootballCommand;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Fixture;

class FetchFixtureStatistics extends Command
{
    protected $signature = 'fetch:fixture-statistics {date}';
    protected $description = 'Fetch and display/store statistics for all fixtures on a given date';

    public function handle()
    {
        $date = $this->argument('date');
        $fixtures = Fixture::whereDate('date', $date)->get();
        $apiUrl = 'https://v3.football.api-sports.io/fixtures';
        $apiKey = env('SPORT_API_KEY');

        if ($fixtures->isEmpty()) {
            $this->info("No fixtures found for $date.");
            return 0;
        }

        foreach ($fixtures as $fixture) {
            $this->info("Fetching statistics for fixture ID: {$fixture->external_id} ({$fixture->home_team_name} vs {$fixture->away_team_name})");
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
            $stats = $data['response'][0]['statistics'] ?? null;
            $players = $data['response'][0]['players'] ?? null;

            // Output to console (or store in DB as needed)
            $this->info('Statistics: ' . json_encode($stats));
            $this->info('Players: ' . json_encode($players));

            // You can add logic here to store statistics in your own tables
        }

        $this->info('All fixture statistics fetched.');
        return 0;
    }
}
