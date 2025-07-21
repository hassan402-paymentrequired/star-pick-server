<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\FetchWeeklyFixturesJob;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('fetch:next-week-fixtures {league} {season}', function ($league, $season) {
    $from = Carbon::now()->next(Carbon::SUNDAY)->toDateString();
    $to = Carbon::parse($from)->addDays(6)->toDateString();
    FetchWeeklyFixturesJob::dispatch($league, $season, $from, $to);
    $this->info("Dispatched job to fetch fixtures for league $league, season $season, from $from to $to.");
})->describe('Dispatch job to fetch next week\'s fixtures for a league and season');
