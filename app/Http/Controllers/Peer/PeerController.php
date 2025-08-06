<?php

namespace App\Http\Controllers\Peer;

use App\Enum\PeerShareRatioEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\BetRequest;
use App\Http\Requests\StorePeerRequest;
use App\Models\Peer;
use App\Utils\Service\V1\Peer\PeerService;
use App\Utils\Service\V1\Player\PlayerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PeerController extends Controller
{

    protected PlayerService $playerService;
    protected PeerService $peerService;

    public function __construct(PlayerService $playerService, PeerService $peerService)
    {
        $this->playerService = $playerService;
        $this->peerService = $peerService;
    }


    public function index()
    {
        $user = auth('web')->user();
        $today = now()->toDateString();
        $tournament = \App\Models\DailyContest::whereDate('created_at', $today)->first();

        $recent = Peer::with('created_by')
            ->whereDoesntHave('users', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->withCount('users')
            ->latest()
            ->take(4)
            ->get();



        $peers = Peer::with('created_by')->whereDoesntHave('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->withCount('users')->latest()->paginate(10);
        return Inertia::render('peers/index', [

            'tournament' => $tournament,
            'recent' => $recent,
            'peers' => $peers,
        ]);
    }

    public function create()
    {
        return Inertia::render('peers/create', [
            'user' => auth('web')->user()->load('wallet')
        ]);
    }

    public function store(StorePeerRequest $request)
    {
        $peer = Peer::where('user_id', Auth::guard('web')->id())->latest()->first();
        if ($peer) {
            if ($peer->users()->count() === 0) {
                return back()->with('error', 'You already have an active peer with no users');
            }
        }

        $peer = Peer::create([
            'name' => $request->name,
            'amount' => $request->amount,
            'private' => $request->private,
            'limit' => $request->limit,
            'user_id' => Auth::guard('api')->id(),
            'sharing_ratio' => $request->ratio ??= PeerShareRatioEnum::DIVIDE->value
        ]);

        $peer->addUser(Auth::id());
        return to_route('peers.show', [
            'peer' => $peer->id
        ])->with('success', 'Peer created successfully');
    }


    public function joinPeer(Peer $peer)
    {
        $players = $this->playerService->groupedByStar();
        // Log::info($players->toArray());
        // dd($players);
        return Inertia::render('peers/join-peer', [
            'peer' => $peer,
            'players' => $players
        ]);
    }

    public function storeJoinPeer(BetRequest $request, Peer $peer)
    {
        $this->peerService->playBet($request, $peer, 'web');
        return to_route('peers.show', [
            'peer' => $peer
        ])->with('success', 'Peer joined successfully');
    }

    public function contents()
    {
        $user = auth('web')->user();

        // Ongoing: Peers the user joined that are currently ongoing
        $ongoingPeers = $user->peers()
            ->where('status', 'open')
            ->with('created_by')
            ->withCount('users')
            ->get();

        // dd($ongoingPeers);

        // History: Peers the user joined that have ended
        $historyPeers = $user->peers()
            ->where('status', 'finished')
            ->with('created_by')
            ->withCount('users')
            // ->orderBy('end_time', 'desc')
            ->get();

        return Inertia::render('peers/contents/index', [
            'ongoing' => $ongoingPeers,
            'history' => $historyPeers,
        ]);
    }

    public function globalPeer()
    {
        return Inertia::render('peers/global/index', [
            //
        ]);
    }

    public function show(Peer $peer)
    {
        $peer = \App\Models\Peer::with('created_by')->findOrFail($peer->id);

        // Get all users who joined this peer, with their squads
        $peerUsers = \App\Models\PeerUser::with(['user', 'squads.mainPlayer', 'squads.subPlayer'])->where('peer_id', $peer->id)->get();

        $users = $peerUsers->map(function ($peerUser) {
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
            ];
        });


        return Inertia::render('peers/show', [
            'peer' => $peer,
            'users' => $users
        ]);
    }
}
