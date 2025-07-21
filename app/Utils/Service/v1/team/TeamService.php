<?php

namespace App\Utils\Service\V1\Team;

use App\Enum\CacheKey;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class TeamService
{
    public function teams(): Collection
    {
        return Cache::remember(CacheKey::TEAMS->value, now()->addDay(), function () {
            return Team::withCount('players')->get();
        });
    }
}
