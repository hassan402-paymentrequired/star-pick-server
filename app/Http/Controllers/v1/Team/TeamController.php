<?php

namespace App\Http\Controllers\V1\Team;

use App\Utils\Service\V1\Team\TeamService;
use Illuminate\Support\Facades\Artisan;

class TeamController extends \App\Http\Controllers\Controller
{

    protected TeamService $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    public function index()
    {
        return $this->teamService->teams();
    }

    public function refetch(Request $request)
    {
        $league = $request->league;
        $season = $request->season;
        Artisan::call('fetch:teams', ['league' => $league, 'season' => $season]);
        return $this->respondWithCustomData(
            [
                'message' => 'Teams refetched successfully'
            ]
        );
    }
}
