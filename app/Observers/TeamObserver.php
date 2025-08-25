<?php

namespace App\Observers;

use App\Enum\CacheKey;
use App\Models\Team;
use Illuminate\Support\Facades\Cache;

class TeamObserver
{
    /**
     * Handle the Team "created" event.
     */
    public function created(Team $team): void
    {
        Cache::forget(CacheKey::TEAMS->value);
    }

    /**
     * Handle the Team "updated" event.
     */
    public function updated(Team $team): void
    {
        Cache::forget(CacheKey::TEAMS->value);
    }

    /**
     * Handle the Team "deleted" event.
     */
    public function deleted(Team $team): void
    {
        Cache::forget(CacheKey::TEAMS->value);
    }

    /**
     * Handle the Team "restored" event.
     */
    public function restored(Team $team): void
    {
        Cache::forget(CacheKey::TEAMS->value);
    }

    /**
     * Handle the Team "force deleted" event.
     */
    public function forceDeleted(Team $team): void
    {
        Cache::forget(CacheKey::TEAMS->value);
    }
}
