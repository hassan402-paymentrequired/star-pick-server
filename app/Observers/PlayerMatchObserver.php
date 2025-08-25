<?php

namespace App\Observers;

use App\Enum\CacheKey;
use App\Models\PlayerMatch;
use Illuminate\Support\Facades\Cache;

class PlayerMatchObserver
{
    /**
     * Handle the PlayerMatch "created" event.
     */
    public function created(PlayerMatch $playerMatch): void
    {
        Cache::forget(CacheKey::MATCH->value);
    }

    /**
     * Handle the PlayerMatch "updated" event.
     */
    public function updated(PlayerMatch $playerMatch): void
    {
        Cache::forget(CacheKey::MATCH->value);
    }

    /**
     * Handle the PlayerMatch "deleted" event.
     */
    public function deleted(PlayerMatch $playerMatch): void
    {
       Cache::forget(CacheKey::MATCH->value);
    }

    /**
     * Handle the PlayerMatch "restored" event.
     */
    public function restored(PlayerMatch $playerMatch): void
    {
        //
    }

    /**
     * Handle the PlayerMatch "force deleted" event.
     */
    public function forceDeleted(PlayerMatch $playerMatch): void
    {
        //
    }
}
