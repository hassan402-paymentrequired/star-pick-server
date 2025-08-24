<?php

namespace App\Http\Controllers\Tournament;

use App\Http\Controllers\Controller;
use App\Models\DailyContest;
use App\Models\DailyContestUser;
use App\Models\User;
use App\Utils\Service\V1\Player\PlayerService;
use App\Utils\Service\V1\Tournament\TournamentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TournamentController extends Controller
{
    protected TournamentService $tournamentService;
    protected PlayerService $playerService;

    public function __construct(TournamentService $tournamentService, PlayerService $playerService)
    {
        $this->tournamentService = $tournamentService;
        $this->playerService = $playerService;
    }

    public function index()
    {
        $tournament = DailyContest::whereDate('created_at', Carbon::today())->first();

        $users =  \App\Models\DailyContestUser::with(['user', 'squads'])->where('daily_contest_id', $tournament->id)->get();

        $users = $users->map(function ($peerUser) {
            $user = $peerUser->user;
            $squads = $peerUser->squads->map(function ($squad) {
                // Get fixture_id for main and sub from player_match
                $mainPlayerMatch = \App\Models\PlayerMatch::find($squad->main_player_match_id);
                $main_fixture_id = $mainPlayerMatch ? $mainPlayerMatch->fixture_id : null;
                $subPlayerMatch = \App\Models\PlayerMatch::find($squad->sub_player_match_id);
                $sub_fixture_id = $subPlayerMatch ? $subPlayerMatch->fixture_id : null;

                // Get main player stats using player_id and fixture_id
                $mainStats = null;
                if ($main_fixture_id) {
                    $mainStats = \App\Models\PlayerStatistic::where('player_id', $squad->main_player_id)
                        ->where('fixture_id', $main_fixture_id)
                        ->first();
                }

                // Get sub player stats using player_id and fixture_id
                $subStats = null;
                if ($sub_fixture_id) {
                    $subStats = \App\Models\PlayerStatistic::where('player_id', $squad->sub_player_id)
                        ->where('fixture_id', $sub_fixture_id)
                        ->first();
                }

                $mainPlayer = $squad->mainPlayer ? $squad->mainPlayer->toArray() : [];
                $mainPlayer['statistics'] = $mainStats ? $mainStats->toArray() : [];

                $subPlayer = $squad->subPlayer ? $squad->subPlayer->toArray() : [];
                $subPlayer['statistics'] = $subStats ? $subStats->toArray() : [];

                return [
                    'id' => $squad->id,
                    'peer_user_id' => $squad->peer_user_id,
                    'star_rating' => $squad->star_rating,
                    'main_player_id' => $squad->main_player_id,
                    'sub_player_id' => $squad->sub_player_id,
                    'main_player_match_id' => $squad->main_player_match_id,
                    'sub_player_match_id' => $squad->sub_player_match_id,
                    'main_player' => $mainPlayer,
                    'sub_player' => $subPlayer,


                ];
            });

            return [
                'id' => $user->id,
                'username' => $user->username,
                'avatar' => $user->avatar,
                'email' => $user->email,
                'created_at' => $user->created_at,
                'squads' => $squads,
                'total_point' => $peerUser->total_points,
                'is_winner' => $peerUser->is_winner,
            ];
        });

        // dd($users);


        return Inertia::render('peers/global/index', [
            'users' => $users,
            'tournament' => $tournament
        ]);
    }


    public function create()
    {
        $user = Auth::guard('web')->user();

        if ($user->AlreadyJoinedTodayTournament()) {
            return to_route('tournament.index')->with('error', 'You have already joined the tournament');
        }


        $today = now()->toDateString();
        $tournament = DailyContest::whereDate('created_at', $today)->withCount('users')->first();
        $players = $this->playerService->groupedByStar();
        return Inertia::render('peers/global/create', [
            'tournament' => $tournament,
            'players' => $players,
            'balance' => getUserBalance('web')
        ]);
    }


    public function store(Request $request)
    {
        $today = now()->toDateString();
        $tournament = \App\Models\DailyContest::whereDate('created_at', $today)->first();
        if (!hasEnoughBalance($tournament->amount, 'web')) {
            return back()->with('error', 'Insufficient balance to join tournament. Please fund your wallet.');
        }

        $request->validate([
            'peers' => ['array', 'min:5', 'max:5'],
            'peers.*.main' => ['required', 'exists:players,id'],
            'peers.*.sub' => ['required', 'exists:players,id'],
            'peers.*.main_player_match_id' => ['required', 'exists:player_matches,id'],
            'peers.*.sub_player_match_id' => ['required', 'exists:player_matches,id'],
        ]);

        if (!$this->tournamentService->create($request, $tournament, 'web')) {
            return to_route('tournament.index')->with('error', 'Tournament joining failed');
        }

        decreaseWallet($tournament->amount, 'web');
        return to_route('tournament.index')->with('success', 'Tournament joined successfully');
    }

    public function show(User $user)
    {
        // dd($user);
        return Inertia::render('peers/global/show');
    }
}
