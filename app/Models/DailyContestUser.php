<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyContestUser extends Model
{
    
    public function tournament()
    {
        return $this->belongsToMany(DailyContest::class);
    }
}
