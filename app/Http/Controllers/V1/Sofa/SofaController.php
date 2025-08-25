<?php

namespace App\Http\Controllers\V1\Sofa;

use App\Http\Controllers\Controller;
use App\Jobs\SaveCountriesJob;
use App\Jobs\SaveLeagues;
use App\Jobs\SavePlayerJob;
use App\Jobs\SaveSeasonJob;
use App\Jobs\SaveTeamJob;
use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SofaController extends Controller
{

    public function countries(Request $request)
    {

        $request->validate([
            'countries' => 'required|array',
        ]);

        SaveCountriesJob::dispatch($request->countries);

        return response()->json(['message' => 'Countries saved successfully']);
    }

    public function leagues(Request $request)
    {
        $request->validate([
            'leagues' => 'required|array',
            'country_id' => 'required|exists:countries,external_id',
        ]);

        SaveLeagues::dispatch($request->leagues, $request->country_id);

        return response()->json(['message' => 'Leagues saved successfully']);
    }

    public function teams(Request $request)
    {
        $request->validate([
            'rows' => 'required|array',
            'league_id' => 'required|exists:leagues,external_id',
        ]);

        SaveTeamJob::dispatch($request->rows, $request->league_id);

        return response()->json(['message' => 'Teams saved successfully']);
    }

    public function seasons(Request $request)
    {
        $request->validate([
            'seasons' => 'required|array',
            'league_id' => 'required|exists:leagues,external_id',
        ]);
        SaveSeasonJob::dispatch($request->seasons, $request->league_id);
        return response()->json(['message' => 'Players saved successfully']);
    }


    public function players(Request $request)
    {
        $request->validate([
            'players' => 'required|array',
            'team_id' => 'required|exists:teams,external_id',
        ]);

        SavePlayerJob::dispatch($request->players, $request->team_id);

        return response()->json(['message' => 'Players saved successfully']);
    }

    public function rounds(Request $request)
    {
        $request->validate([
            'rounds' => 'required|array',
            'season_id' => 'required|exists:seasons,external_id',
            'league_id' => 'required|exists:leagues,external_id',
            'current' => 'integer|nullable',
        ]);

        $s = Season::where('external_id', $request->season_id)->first();

        Log::info($request->rounds);

        foreach ($request->rounds['rounds'] as $round) {

            Log::info('Saving round: ' . json_encode($round));


            \App\Models\Round::updateOrCreate(
                ['round' => $round['round']],
                [
                    'season_id' => $s->id,
                    'league_id' => $request->league_id,
                    'is_current' => $round['round'] === $request->current ? true : false,
                ]
            );
        }

        return response()->json(['message' => 'Rounds saved successfully']);
    }
}
