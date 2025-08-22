<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    /** @use HasFactory<\Database\Factories\SeasonFactory> */
    use HasFactory;


    public function league()
    {
        return $this->belongsTo(League::class, 'league_id');
    }

    public function rounds()
    {
        return $this->hasMany(Round::class);
    }
}
