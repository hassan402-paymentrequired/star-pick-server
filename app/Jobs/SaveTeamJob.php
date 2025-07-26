<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SaveTeamJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public array $teams, public string $leagueId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->teams as $row) {
            \App\Models\Team::updateOrCreate(
                ['external_id' => $row['team']['id']],
                [
                    'name' => $row['team']['name'] ?? null,
                    'code' => $row['team']['nameCode'] ?? null,
                    'logo' => $row['team']['slug'] ?? null,
                    'country' => $row['team']['country']['name'] ?? null,
                ]
            );
        }
    }
}
