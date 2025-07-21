<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fixture extends Model
{
    protected $fillable = [
        'external_id',
        'league_id',
        'season',
        'date',
        'timestamp',
        'venue_id',
        'venue_name',
        'venue_city',
        'home_team_id',
        'home_team_name',
        'home_team_logo',
        'away_team_id',
        'away_team_name',
        'away_team_logo',
        'status',
        'goals_home',
        'goals_away',
        'score_halftime_home',
        'score_halftime_away',
        'score_fulltime_home',
        'score_fulltime_away',
        'raw_json',
    ];

    protected $casts = [
        'date' => 'datetime',
        'raw_json' => 'array',
    ];
}
