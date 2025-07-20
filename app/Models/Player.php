<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Player extends Model
{
    /** @use HasFactory<\Database\Factories\PlayerFactory> */
    use HasFactory;


    public function bets(): HasMany
    {
        return $this->hasMany(Bet::class);
    }

    public function teams()
    {
        return $this->belongsTo(Team::class, 'team', 'external_id');
    }
}
