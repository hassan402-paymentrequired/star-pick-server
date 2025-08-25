<?php

namespace App\Console\Commands\ApiFootballCommand;

use App\Models\PlayerStatistic;
use Illuminate\Console\Command;

class getAllMatchPlayerStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-all-match-player-stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        PlayerStatistic::updateOrCreate([
            'player_id' => $playerId,
            'match_date' => $matchDate,
        ], [
            'goals' => $api['goals'],
            'assists' => $api['assists'],
            'shots' => $api['shots'],
            'shots_on_target' => $api['shots_on_target'],
            'yellow_cards' => $api['yellow_cards'],
            'did_play' => $api['did_play'],
            'is_injured' => $api['injured'],
        ]);


        foreach ($peer->users as $peerUser) {
            $totalPoints = 0;

            foreach ($peerUser->squads as $squad) {
                $player = null;

                // Check if main player played
                $mainStats = PlayerStatistic::where('player_id', $squad->main_player_id)
                    ->where('match_date', $matchDate)->first();

                if ($mainStats && $mainStats->did_play) {
                    $player = $mainStats;
                } else {
                    // Use sub
                    $subStats = PlayerStatistic::where('player_id', $squad->sub_player_id)
                        ->where('match_date', $matchDate)->first();

                    if ($subStats) {
                        $player = $subStats;
                    }
                }

                if ($player) {
                    $totalPoints += $player->points;
                }
            }

            // Update user total points
            $peerUser->update(['total_points' => $totalPoints]);
        }

        $winner = $peer->users->sortByDesc('total_points')->first();

        $peer->update([
            'status' => 'finished',
            'winner_user_id' => $winner->user_id,
        ]);

        $winner->update(['is_winner' => true]);
    }
}
