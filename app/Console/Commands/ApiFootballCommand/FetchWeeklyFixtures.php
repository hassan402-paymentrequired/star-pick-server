<?php

namespace App\Console\Commands\ApiFootballCommand;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Fixture;

class FetchWeeklyFixtures extends Command
{
    protected $signature = 'fetch:weekly-fixtures {league} {season} {from} {to}';
    protected $description = 'Fetch all fixtures for a league and season within a date range and upsert them into the fixtures table';

    public function handle()
    {
        $league = $this->argument('league');
        $season = $this->argument('season');
        $from = $this->argument('from');
        $to = $this->argument('to');
        $apiUrl = 'https://v3.football.api-sports.io/fixtures';
        $apiKey = env('SPORT_API_KEY');
        $page = 1;
        $totalPages = 1;

        do {
            $this->info("Fetching fixtures for league $league, season $season, from $from to $to, page $page...");
            $response = Http::withHeaders([
                'x-rapidapi-key' => $apiKey
            ])->get($apiUrl, [
                'league' => $league,
                'season' => $season,
                'from' => $from,
                'to' => $to,
                // 'page' => $page
            ]);

            if (!$response->ok()) {
                $this->error('Failed to fetch fixtures: ' . $response->body());
                return 1;
            }

            $body = $response->json();
            $fixtures = $body['response'] ?? [];
            $paging = $body['paging'] ?? ['current' => $page, 'total' => $page];
            $currentPage = $paging['current'] ?? $page;
            $totalPages = $paging['total'] ?? $page;

            $this->info("Total fixtures fetched: " . count($fixtures));
            // $this->info(json_encode($body));

            foreach ($fixtures as $item) {
                $fixture = $item['fixture'];
                $leagueData = $item['league'];
                $teams = $item['teams'];
                $venue = $fixture['venue'] ?? [];
                $goals = $item['goals'] ?? [];
                $score = $item['score'] ?? [];
                $halftime = $score['halftime'] ?? [];
                $fulltime = $score['fulltime'] ?? [];
                Fixture::updateOrCreate(
                    [
                        'external_id' => $fixture['id'],
                    ],
                    [
                        'league_id' => $leagueData['id'],
                        'season' => $leagueData['season'],
                        'date' => $fixture['date'],
                        'timestamp' => $fixture['timestamp'],
                        'venue_id' => $venue['id'] ?? null,
                        'venue_name' => $venue['name'] ?? null,
                        'venue_city' => $venue['city'] ?? null,
                        'home_team_id' => $teams['home']['id'],
                        'home_team_name' => $teams['home']['name'],
                        'home_team_logo' => $teams['home']['logo'] ?? null,
                        'away_team_id' => $teams['away']['id'],
                        'away_team_name' => $teams['away']['name'],
                        'away_team_logo' => $teams['away']['logo'] ?? null,
                        'status' => $fixture['status']['long'] ?? null,
                        'goals_home' => $goals['home'] ?? null,
                        'goals_away' => $goals['away'] ?? null,
                        'score_halftime_home' => $halftime['home'] ?? null,
                        'score_halftime_away' => $halftime['away'] ?? null,
                        'score_fulltime_home' => $fulltime['home'] ?? null,
                        'score_fulltime_away' => $fulltime['away'] ?? null,
                        'raw_json' => json_encode($item),
                    ]
                );
            }

            $page++;
        } while ($page <= $totalPages);

        $this->info('All fixtures fetched and upserted successfully.');
    }
}
