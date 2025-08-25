<?php

namespace App\Console\Commands\SofaScoreCommand;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchCountries extends Command
{
    protected $signature = 'sofa:fetch:countries';
    protected $description = 'Fetch countries from SofaScore API';


    public function handle()
    {
        $url = 'https://www.sofascore.com/api/v1/sport/football/categories';

        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_HTTPGET => 1,
            CURLOPT_HTTPHEADER => array(
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Referer' => 'https://www.sofascore.com/',
                'Accept-Language' => 'en-US,en;q=0.9',
                'User-Agent' => 'Mozilla/5.0 (Linux; Android 10; SM-G973F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.120 Mobile Safari/537.36',
            ),
            CURLOPT_RETURNTRANSFER => true,
        ));

        $response = curl_exec($ch);
        curl_close($ch);

        $t = json_decode($response, true);
        dd($t);
    }



    /**
     * Retry an HTTP GET request on 403 or failure.
     */
    private function retryRequest(string $url, int $maxRetries, int $delaySeconds)
    {
        $tries = 0;

        while ($tries < $maxRetries) {
            $tries++;

            $headers = [
                'Accept' => 'application/json',
                'Accept-Language' => 'en-US,en;q=0.9',
                'Referer' => 'https://www.sofascore.com/',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/138.0.0.0 Safari/537.36',
            ];

            try {
                $response = Http::withHeaders($headers)
                    ->withCookies([
                        'hb_insticator_uid' => '625625e8-df10-40df-8e6f-66135018fcff',
                        '_ga' => 'GA1.1.735689633.1753401781',
                        // Add more relevant cookies only if needed
                    ], '.sofascore.com')
                    ->get($url);

                if ($response->status() !== 403) {
                    return $response;
                }

                $this->warn("403 Forbidden - retrying in {$delaySeconds}s (Attempt {$tries}/{$maxRetries})...");
                sleep($delaySeconds);
            } catch (\Exception $e) {
                $this->error("Error: {$e->getMessage()} - retrying...");
                sleep($delaySeconds);
            }
        }

        return null;
    }
}
