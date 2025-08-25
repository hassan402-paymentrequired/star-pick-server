<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    /** @use HasFactory<\Database\Factories\LeaguesFactory> */
    use HasFactory;

    public function country()
    {
        return $this->belongsTo(Country::class, 'country');
    }


    public function seasons()
    {
        return $this->hasMany(Season::class);
    }

    public function rounds()
    {
        return $this->hasMany(Round::class);
    }

    public function getCurrentSeason()
    {
        return $this->seasons()->where('is_current', true)->first();
    }
}
