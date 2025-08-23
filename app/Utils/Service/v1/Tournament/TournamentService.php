<?php

namespace App\Utils\Service\V1\Tournament;

use Illuminate\Support\Facades\Auth;

class TournamentService
{
    public function create($request, $tournament = null, $guard = 'api')
    {
        if ($tournament->users()->where('user_id', Auth::guard($guard)->id())->exists()) {
            return false;
        }

        // Create peer_user record
        $contestUser = \App\Models\DailyContestUser::create([
            'daily_contest_id' => $tournament->id,
            'user_id' => Auth::guard($guard)->id(),
            'total_points' => 0,
            'is_winner' => false
        ]);

        // Create peer_user_squad records for each squad member
        foreach ($request->peers as $value) {
            \App\Models\DailyContestUserSquard::create([
                'daily_contest_user_id' => $contestUser->id,
                'star_rating' => $value['star'] ?? 1,
                'main_player_id' => $value['main'],
                'sub_player_id' => $value['sub'],
                'main_player_match_id' => $value['main_player_match_id'],
                'sub_player_match_id' => $value['sub_player_match_id'],
            ]);
        }
        return true;
    }
}
