<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

class FetchWeeklyFixturesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $league;
    protected $season;
    protected $from;
    protected $to;

    public function __construct($league, $season, $from, $to)
    {
        $this->league = $league;
        $this->season = $season;
        $this->from = $from;
        $this->to = $to;
    }

    public function handle()
    {
        Artisan::call('fetch:weekly-fixtures', [
            'league' => $this->league,
            'season' => $this->season,
            'from' => $this->from,
            'to' => $this->to,
        ]);
    }
}
