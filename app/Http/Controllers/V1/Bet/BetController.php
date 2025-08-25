<?php

namespace App\Http\Controllers\V1\Bet;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlaceBetRequest;
use App\Utils\Service\V1\Bet\BetService;
use Illuminate\Http\JsonResponse;

class BetController extends Controller
{
    protected BetService $betService;

    public function __construct(BetService $betService)
    {
        $this->betService = $betService;
    }

    public function store(PlaceBetRequest $request): JsonResponse
    {
        $this->betService->placeBet($request);
        return $this->respondWithCustomData([
            'message' => 'bet placed successfully'
        ], 201);
    }

    function index()
    {
        $bets = $this->betService->getBets(request());
        return $this->respondWithCustomData([
            'bookings' => $bets
        ], 200);
    }

    // public function show($id)
    // {
    // # code...
    // }
}
 