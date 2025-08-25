<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Peer extends Model
{
    /** @use HasFactory<\Database\Factories\PeerFactory> */
    use HasFactory;

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->peer_id = (string) \Illuminate\Support\Str::random(6);
        });
    }

    public function created_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'peer_users')->withTimestamps();
    }


    public function bets(): HasMany
    {
        return $this->hasMany(Bet::class);
    }

    public function addUser(string $id): void
    {
        $this->users()->attach($id);
    }

    public function removeUser(string $id): void
    {
        $this->users()->detach($id);
    }

    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_user_id');
    }
}
