<?php

namespace App\Http\Controllers\Peer;

use App\Http\Controllers\Controller;
use App\Models\Peer;
use App\Utils\Service\V1\Peer\PeerService;
use App\Utils\Service\V1\Player\PlayerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PeerController extends Controller
{

     protected PlayerService $playerService;
     protected PeerService $peerService;

    public function __construct(PlayerService $playerService,PeerService $peerService)
    {
        $this->playerService = $playerService;
        $this->peerService = $peerService;
    }


    public function index()
    {
        $recent = Peer::with('created_by')->withCount('users')->latest()->take(4)->get();

        // dd($recent);

        $peers = Peer::with('created_by')->withCount('users')->latest()->paginate(10);
        return Inertia::render('peers/index', [
            'recent' => $recent,
            'peers' => $peers,
        ]);
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

    public function storeJoinPeer(Request $request, Peer $peer)
    {
        $this->peerService->playBet($request, $peer);
        return to_route('peers.show', [
            'peer' => $peer
        ]);
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



        // History: Peers the user joined that have ended
        $historyPeers = $user->peers()
            ->where('status', 'open')
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
}
