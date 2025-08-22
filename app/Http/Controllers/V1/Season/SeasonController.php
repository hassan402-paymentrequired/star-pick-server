<?php

namespace App\Http\Controllers\V1\Season;

use App\Http\Controllers\Controller;

class SeasonController extends Controller
{

    public function index()
    {
        $seasons = \App\Models\Season::where('is_current', true)->with('league')->get();

        return $this->respondWithCustomData([
            'seasons' => $seasons,
        ]);
    }
}
