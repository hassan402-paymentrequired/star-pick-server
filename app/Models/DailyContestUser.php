<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyContestUser extends Model
{

    public function daily_contest()
    {
        return $this->belongsTo(DailyContest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function squads()
    {
        return $this->hasMany(DailyContestUserSquard::class);
    }
}
