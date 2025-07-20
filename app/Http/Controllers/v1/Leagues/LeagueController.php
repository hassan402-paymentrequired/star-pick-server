<?php

namespace App\Http\Controllers\V1\Leagues;

use App\Http\Controllers\Controller;
use App\Models\Leagues;
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
}
