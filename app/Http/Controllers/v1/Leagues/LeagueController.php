<?php

namespace App\Http\Controllers\V1\Leagues;

use App\Http\Controllers\Controller;
use App\Models\Leagues;
use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class LeagueController extends Controller
{
    public function index()
    {
        $leagues = Leagues::all();

        return $this->respondWithCustomData(
            [
                'leagues' => $leagues
            ]
        );
    }

    public function refetch(Request $request)
    {
        $name = $request->country_name ?? '';
        $season = $request->season ?? 2023;
        Artisan::call('fetch:leagues', ['country' => $name, 'season' => $season]);

        return $this->respondWithCustomData(
            [
                'message' => 'Leagues refetched successfully'
            ]
        );
    }

    public function getLeagueSeason(string $leagueId)
    {
    
        $season = Season::where('league_id', $leagueId)->where('is_current', true)->first();

        return $this->respondWithCustomData(
            [
                'seasons' => $season
            ]
        );
    }

    public function getLeagueSeasonAndRound(Leagues $leagues)
    {
        $league = $leagues;
       
        $seasons = Season::where('league_id', $league->id)
            ->with(['league'])
            ->get();

        return $this->respondWithCustomData(
            [
                'seasons' => $seasons
            ]
        );
    }
}
