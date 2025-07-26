<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Round extends Model
{
    /** @use HasFactory<\Database\Factories\RoundFactory> */
    use HasFactory;

    function season()
    {
        return $this->belongsTo(Season::class, 'season_id');
    }

    function league()
    {
        return $this->belongsTo(Leagues::class, 'league_id');
    }
}
