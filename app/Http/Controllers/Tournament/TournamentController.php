<?php

namespace App\Http\Controllers\Tournament;

use App\Http\Controllers\Controller;
use App\Utils\Service\V1\Tournament\TournamentService;
use Inertia\Inertia;

class TournamentController extends Controller
{
    protected TournamentService $tournamentService;

    public function __construct(TournamentService $tournamentService)
    {
        $this->tournamentService = $tournamentService;
    }

    public function index()
    {
        $today = now()->toDateString();
        $tournament = \App\Models\DailyContest::whereDate('created_at', $today)->first();
        return Inertia::render('peers/global/index', [
            'tournament' => $tournament
        ]);
    }
}
