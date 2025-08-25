<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SaveLeagues implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public array $leagues, public string $countryId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->leagues as $league) {
            \App\Models\Leagues::updateOrCreate(
                ['external_id' => $league['id']],
                [
                    'name' => $league['name'] ?? null,
                    'country' => $this->countryId,
                    'country_flag' => $league['slug'] ??  $this->countryId,
                    'external_id' => $league['id'] ?? null,
                ]
            );
        }
    }
}
