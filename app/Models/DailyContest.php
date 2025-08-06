<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyContest extends Model
{
    
    public function users()
    {
        return $this->belongsToMany(User::class, 'daily_contest_users')->withTimestamps();
    }
}
