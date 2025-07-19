<?php

namespace App\Http\Controllers\V1\Team;

use App\Utils\Service\V1\Team\TeamService;

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
}
