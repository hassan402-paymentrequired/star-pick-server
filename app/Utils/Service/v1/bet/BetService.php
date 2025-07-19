<?php

namespace App\Utils\Service\V1\Bet;

use App\Models\Bet;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class BetService
{
    public function placeBet(Request $request): void {}

    public function getBets(Request $request): Collection
    {
        return Bet::where('user_id', $request->user()->id)->get();
    }

    public function getBetsByPeer(Request $request): Collection
    {
        return Bet::where('peer_id', $request->peer_id)->get();
    }

    public function getBetsByPlayer(Request $request): Collection
    {
        return Bet::where('player_id', $request->player_id)->get();
    }

    public function getBetsByDate(Request $request): Collection
    {
        return Bet::where('date', $request->date)->get();
    }
}
