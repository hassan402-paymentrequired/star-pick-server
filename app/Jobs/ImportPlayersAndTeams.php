<?php

namespace App\Jobs;

use App\Models\Player;
use App\Models\Team;
use App\Utils\Helper\HelperService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ImportPlayersAndTeams implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $res = HelperService::getAllPlayerAndTeams();
        collect($res['teams']->response)->chunk(50)->each(function ($chunk) {
            foreach ($chunk as $value) {
                Team::updateOrCreate(
                    ['external_id' => $value->team->id],
                    [
                        'name' => $value->team->name,
                        'code' => $value->team->code,
                        'country' => $value->team->country,
                        'logo' => $value->team->logo
                    ]
                );
            }
            usleep(100000);
        });



        collect($res['players'])->chunk(50)->each(function ($chunk) {
            foreach ($chunk as $value) {
                Player::updateOrCreate([
                    'external_id' => $value->player->id
                ], [
                    'name' => $value->player->name,
                    'team' => $value->statistics[0]->team->id ?? null,
                    'position' => $value->statistics[0]->games->position ?? null,
                    'image' => $value->player->photo,
                    'nationality' => $value->player->nationality
                ]);
                usleep(100000);
            }
        });
    }

   
}
