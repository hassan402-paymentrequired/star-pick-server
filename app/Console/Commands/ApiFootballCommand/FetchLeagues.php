<?php

namespace App\Console\Commands\ApiFootballCommand;

use App\Models\Leagues;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class FetchLeagues extends Command
{
    protected $signature = 'fetch:leagues {country?}';
    protected $description = 'Truncate leagues table and fetch all leagues from the API';

    public function handle()
    {
        $country = $this->argument('country');
        $this->info('Deleting all leagues...');

        $page = 1;
        $totalPages = 1;
        $insertBatch = [];

        do {
            $this->info("Fetching page $page...");

            $params = [
                'season' => 2023,
            ];


            $response = Http::withHeaders([
                'x-rapidapi-key' => env('SPORT_API_KEY')
            ])->get("https://v3.football.api-sports.io/leagues", [
                'country' => strtolower($country ?? ''),
            ]);

            $body = $response->json();
            $totalPages = $body['paging']['total'] ?? 1;
            $leagues = $body['response'] ?? [];
            $count = count($leagues);
            $this->info("Total league $count...");

            foreach ($leagues as $item) {
                Leagues::updateOrCreate(
                    [
                        'external_id' => $item['league']['id'],
                    ],
                    [
                        'name'         => $item['league']['name'],
                        'type'         => $item['league']['type'],
                        'logo'         => $item['league']['logo'],
                        'country'      => $item['country']['name'],
                        'country_flag' => $item['country']['flag'] ?? '',
                        'season'       => json_encode($item['seasons']),
                        'updated_at'   => now(),
                    ]
                );
            }

            $page++;
        } while ($page <= $totalPages);

        $this->info('All leagues fetched and upserted successfully.');
    }

    private function bulkInsert(array $batch)
    {
        Leagues::insert($batch);
    }
}
