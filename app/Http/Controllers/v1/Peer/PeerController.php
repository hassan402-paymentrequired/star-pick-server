<?php

namespace App\Http\Controllers\V1\Peer;

use App\Http\Controllers\Controller;
use App\Http\Requests\BetRequest;
use App\Http\Requests\StorePeerRequest;
use App\Models\Peer;
use App\Utils\Helper\HelperService;
use App\Utils\Service\V1\Peer\PeerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Enum\CacheKey;

class PeerController extends Controller
{
    protected PeerService $peerService;

    public function __construct(PeerService $peerService)
    {
        $this->peerService = $peerService;
    }

    public function index(): JsonResponse
    {
        $peers = $this->peerService->getPeers();
        return $this->respondWithCustomData([
            'peers' => $peers['peers'],
            'recent' => $peers['recents']
        ], 200);
    }

    public function store(StorePeerRequest $request): JsonResponse
    {
       $peer = $this->peerService->createPeer($request);
        return $this->respondWithCustomData([
            'message' => 'peer created successfully',
            'peer' => $peer
        ], 200);
    }

    public function show(Peer $peer): JsonResponse
    {
        $peer = Cache::remember(CacheKey::PEERS->value . $peer->id, now()->addHours(1), function () use($peer) {
            return $peer->load(['users', 'created_by']);
        });
        return $this->respondWithCustomData([
            'peer' => $peer
        ], 200);
    }

    public function update(Request $request, Peer $peer): JsonResponse
    {
        $this->peerService->updatePeer($request, $peer);
        return $this->respondWithCustomData([
            'message' => 'peer updated successfully'
        ], 200);
    }

    public function destroy(Peer $peer): JsonResponse
    {
        if ($peer->users()->count() > 1) {
            return $this->respondWithCustomData([
                'message' => 'peer cannot be deleted because it has users'
            ], 400);
        }
        $this->peerService->deletePeer($peer);
        return $this->respondWithCustomData([
            'message' => 'peer deleted successfully'
        ], 200);
    }

    public function joinPeer(BetRequest $request, Peer $peer): JsonResponse
    {
        HelperService::checkBalance($request->amount);
        $this->peerService->playBet($request, $peer);
        return $this->respondWithCustomData([
            'message' => 'peer joined placed successfully'
        ], 200);
    }

    public function playBet(Peer $peer): void
    {
        $peer->addUser(Auth::id());
    }

    public function leavePeer(Peer $peer): JsonResponse
    {
        $peer->removeUser(Auth::id());
        return $this->respondWithCustomData([
            'message' => 'join exited successfully'
        ], 200);
    }
}
