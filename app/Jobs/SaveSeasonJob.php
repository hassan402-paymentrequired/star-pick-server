<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SaveSeasonJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public array $seasons, public string $leagueId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
            Log::info($this->seasons);

        $league = \App\Models\Leagues::where('external_id', $this->leagueId)->first()->id;
        
        foreach ($this->seasons['seasons'] as $season) {
            \App\Models\Season::updateOrCreate(
                ['external_id' => $season['id']],
                [
                    'name' => $season['name'] ?? null,
                    'year' => $season['year'] ?? null,
                    'league_id' => $league,
                ]
            );
        }
    }
}
