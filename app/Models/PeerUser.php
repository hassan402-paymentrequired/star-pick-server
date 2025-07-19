<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeerUser extends Model
{
    /** @use HasFactory<\Database\Factories\PeerUserFactory> */
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function peer()
    {
        return $this->belongsTo(Peer::class);
    }

    public function squads()
    {
        return $this->hasMany(PeerUserSquad::class);
    }
}
