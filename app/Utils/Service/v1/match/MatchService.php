<?php

namespace App\Utils\Service\V1\Match;

use App\Enum\CacheKey;
use App\Models\Leagues;
use App\Models\PlayerMatch;
use App\Models\Team;
use Illuminate\Support\Facades\Cache;

class MatchService
{
    public function matches(): array
    {
        $matches = Cache::remember(
            CacheKey::MATCH->value,
            now()->addDay(),
            function () {
                return PlayerMatch::with(
                    ['player' => function ($query) {
                        return $query->with('team');
                    }, 'team', 'league']
                )->get();
            }
        );

        $team = Team::select('id', 'name')->get();
        $leagues = Leagues::select('id', 'name')->limit(50)->get();
        $groupedMatches = $matches->groupBy('league.name');
        return [$groupedMatches, $team, $leagues];
    }
}
