<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SavePlayerJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public array $players,
        public string $teamId
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info($this->players);

        foreach ($this->players as $player) {
            \App\Models\Player::updateOrCreate(
                ['external_id' => $player['player']['id']],
                [
                    'name'         => $player['player']['name'],
                    'team_id'      => $this->teamId,
                    'position'     => $player['player']['position'] ?? '',
                    'image'        => $player['player']['shortName'] ?? '',
                    'nationality'  => $player['player']['country']['name'] ?? '',
                    'player_rating' => random_int(1, 5),
                ]
            );
        }
    }
}
