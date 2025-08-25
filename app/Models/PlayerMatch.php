<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerMatch extends Model
{
    protected $fillable = [
        'date',
        'time',
        'is_completed',
        'player_id',
        'team_id',
        'fixture_id',
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = (string) \Illuminate\Support\Str::random(6);
        });
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function league(): BelongsTo
    {
        return $this->belongsTo(Leagues::class);
    }

    public function fixture(): BelongsTo
    {
        return $this->belongsTo(Fixture::class);
    }

    public function statistics(): BelongsTo
    {
        return $this->belongsTo(PlayerStatistic::class, 'player_id', 'player_id')
            ->where('fixture_id', $this->fixture_id);
    }
}
