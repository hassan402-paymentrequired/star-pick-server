<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeerUserSquad extends Model
{
    public function peerUser()
    {
        return $this->belongsTo(PeerUser::class);
    }

    public function mainPlayer()
    {
        return $this->belongsTo(Player::class, 'main_player_id');
    }

    public function subPlayer()
    {
        return $this->belongsTo(Player::class, 'sub_player_id');
    }
}
