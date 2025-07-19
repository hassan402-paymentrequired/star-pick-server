<?php

namespace App\Console\Commands;

use App\Models\Leagues;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class FetchLeagues extends Command
{
    protected $signature = 'fetch:leagues';
    protected $description = 'Truncate leagues table and fetch all leagues from the API';

    public function handle()
    {
        $this->info('Deleting all leagues...');
        Leagues::query()->delete();
        DB::statement("ALTER TABLE leagues AUTO_INCREMENT = 1");

        $page = 1;
        $totalPages = 1;
        $insertBatch = [];

        do {
            $this->info("Fetching page $page...");

            $response = Http::withHeaders([
                'x-rapidapi-key' => env('SPORT_API_KEY')
            ])->get("https://v3.football.api-sports.io/leagues", [
                'season' => 2023,
            ]);

            $body = $response->json();
            $totalPages = $body['paging']['total'] ?? 1;
            $leagues = $body['response'] ?? [];
            $count = count($leagues);
            $this->info("Total league $count...");
            // $this->info("Total response $count...");

            foreach ($leagues as $item) {
                $insertBatch[] = [
                    'external_id'  => $item['league']['id'],
                    'name'         => $item['league']['name'],
                    'type'         => $item['league']['type'],
                    'logo'         => $item['league']['logo'],
                    'country'      => $item['country']['name'],
                    'country_flag' => $item['country']['flag'] ?? '',
                    'season'       => json_encode($item['seasons']),
                    'created_at'   => now(),
                    'updated_at'   => now()
                ];
            }

            // Insert in batches of 500
            if (count($insertBatch) >= 500) {
                $this->bulkInsert($insertBatch);
                $insertBatch = [];
            }

            $page++;
        } while ($page <= $totalPages);

        // Insert any remaining records
        if (!empty($insertBatch)) {
            $this->bulkInsert($insertBatch);
        }

        $this->info('All leagues fetched and inserted successfully.');
    }

    private function bulkInsert(array $batch)
    {
        Leagues::insert($batch);
    }
}
