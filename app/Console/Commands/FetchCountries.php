<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Country;

class FetchCountries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-countries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apiUrl = 'https://v3.football.api-sports.io/countries';
        $apiKey = env('SPORT_API_KEY');
        $page = 1;
        $totalPages = 1;
        $updated = 0;

        do {
            $response = Http::withHeaders([
                'x-rapidapi-key' => $apiKey,
            ])->get($apiUrl);

            if (!$response->ok()) {
                $this->error('Failed to fetch countries: ' . $response->body());
                return 1;
            }

            $data = $response->json();
            $countries = $data['response'] ?? [];
            $paging = $data['paging'] ?? ['current' => $page, 'total' => $page];
            $totalPages = $paging['total'] ?? 1;


            foreach ($countries as $country) {
                Country::updateOrCreate(
                    [
                        'code' => $country['code'],
                    ],
                    [
                        'name' => $country['name'],
                        'flag' => $country['flag'] ?? null,
                        'external_id' => $country['id'] ?? null,
                    ]
                );
                $updated++;
            }

            $this->info("Fetched page $page of $totalPages, updated $updated countries so far.");
            $page++;
        } while ($page <= $totalPages);

        $this->info('Countries fetch complete.');
        return 0;
    }
}
