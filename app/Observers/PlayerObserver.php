<?php

namespace App\Observers;

use App\Models\Player;
use Illuminate\Support\Facades\Cache;

class PlayerObserver
{
    /**
     * Handle the Player "created" event.
     */
    public function created(Player $player): void
    {
        Cache::tags('players')->flush();
    }

    /**
     * Handle the Player "updated" event.
     */
    public function updated(Player $player): void
    {
        Cache::tags('players')->flush();
    }

    /**
     * Handle the Player "deleted" event.
     */
    public function deleted(Player $player): void
    {
        Cache::tags('players')->flush();
    }

    /**
     * Handle the Player "restored" event.
     */
    public function restored(Player $player): void
    {
        //
    }

    /**
     * Handle the Player "force deleted" event.
     */
    public function forceDeleted(Player $player): void
    {
        //
    }
}
