<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SaveCountriesJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public array $countries)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->countries as $country) {
            \App\Models\Country::updateOrCreate(
                ['external_id' => $country['id']],
                [
                    'code' => $country['alpha2'] ?? null,
                    'name' => $country['name'] ?? null,
                    'flag' => $country['flag'] ?? null,
                    'external_id' => $country['id'] ?? null,
                    'status' => 'active',
                ]
            );
        }
    }
}
