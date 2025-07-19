<?php

namespace App\Http\Controllers\V1\Match;

use App\Http\Controllers\Controller;
use App\Utils\Service\V1\Match\MatchService;
use Illuminate\Http\JsonResponse;

class MatchController extends Controller
{
    private MatchService $matchService;

    public function __construct(MatchService $matchService)
    {
        $this->matchService = $matchService;
    }

    public function index(): JsonResponse
    {
       $matches =  $this->matchService->matches();
        return $this->respondWithCustomData([
            'matches' => $matches[0],
            'team' => $matches[1],
            'leagues' => $matches[2],
        ], 200);
    }
}
