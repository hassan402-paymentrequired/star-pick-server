<?php

namespace App\Http\Controllers\V1\Fixture;

use App\Http\Controllers\Controller;
use App\Models\Fixture;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class FixtureController extends Controller
{
    public function index()
    {
        return $this->respondWithCustomData([
            'fixtures' => Fixture::all()
        ], 200);
    }

    public function refetch(Request $request)
    {
        $league = $request->league ?? '39';
        $season = $request->season ?? '2023';
        $from = $request->from ?? '2021-07-01';
        $to = $request->to ?? '2023-10-31';

        Artisan::call('fetch:weekly-fixtures', ['league' => $league, 'season' => $season, 'from' => $from, 'to' => $to]);
        return $this->respondWithCustomData([
            'message' => 'Fixtures refetched successfully'
        ], 200);
    }
}
