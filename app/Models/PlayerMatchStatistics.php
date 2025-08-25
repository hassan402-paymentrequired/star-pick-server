<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerMatchStatistics extends Model
{
    /** @use HasFactory<\Database\Factories\PlayerMatchStatisticsFactory> */
    use HasFactory;

    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

 
    public function match(): BelongsTo
    {
        return $this->belongsTo(PlayerMatch::class);
    }
}
