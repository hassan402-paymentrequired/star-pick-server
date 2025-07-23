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
use App\Models\PeerUser;
use App\Models\PlayerMatch;
use App\Models\PlayerStatistic;
use Exception;
use Illuminate\Container\Attributes\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log as FacadesLog;

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

    public function show1(Peer $peer): JsonResponse
    {
        $peer = Cache::remember(CacheKey::PEERS->value . $peer->id, now()->addHours(1), function () use ($peer) {
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

    /**
     * Return all peers the authenticated user belongs to
     */
    public function myPeers(): JsonResponse
    {
        $userId = Auth::guard('api')->id();
        $peerUsers = \App\Models\PeerUser::with(['peer', 'squads'])
            ->where('user_id', $userId)
            ->get();

        $peers = $peerUsers->map(function ($peerUser) {
            return [
                'peer' => [
                    'id' => $peerUser->peer->id,
                    'name' => $peerUser->peer->name,
                    'status' => $peerUser->peer->status,
                    'entry_fee' => $peerUser->peer->amount,
                ],
                'total_points' => $peerUser->total_points,
                'is_winner' => $peerUser->is_winner,
                'squad' => $peerUser->squads->map(function ($squad) {
                    return [
                        'star' => $squad->star_rating,
                        'main_player_id' => $squad->main_player_id,
                        'sub_player_id' => $squad->sub_player_id,
                    ];
                }),
            ];
        });

        return $this->respondWithCustomData([
            'peers' => $peers
        ], 200);
    }

    /**
     * Return all ongoing (open) peers the authenticated user belongs to
     */
    public function myOngoingPeers(): JsonResponse
    {
        $userId = Auth::guard('api')->id();

        $peers = Auth::guard('api')->user()->peers()
            ->where('status', 'open')
            ->with('created_by')
            ->withCount('users')
            ->get();

        return $this->respondWithCustomData([
            'peers' => $peers
        ], 200);
    }

    /**
     * Return all completed (finished/closed) peers the authenticated user belongs to
     */
    public function myCompletedPeers(): JsonResponse
    {
        $userId = Auth::guard('api')->id();
        $peerUsers = \App\Models\PeerUser::with('peer')
            ->where('user_id', $userId)
            ->whereHas('peer', function ($q) {
                $q->whereIn('status', ['finished', 'closed']);
            })
            ->get();

        $peers = $peerUsers->map(function ($peerUser) {
            $peer = $peerUser->peer;
            return [
                'id' => $peer->id,
                'name' => $peer->name,
                'status' => $peer->status,
                'entry_fee' => $peer->amount,
                'total_users' => $peer->users()->count(),
            ];
        });

        return $this->respondWithCustomData([
            'peers' => $peers
        ], 200);
    }



    public function show($id)
    {
        try {
            $peer = \App\Models\Peer::with('created_by')->findOrFail($id);

            // Get all users who joined this peer, with their squads
            $peerUsers = \App\Models\PeerUser::with(['user', 'squads.mainPlayer', 'squads.subPlayer'])->where('peer_id', $peer->id)->get();
            return $peerUsers;

            $users = $peerUsers->map(function ($peerUser) {
                // dd($peerUser);
                $user = $peerUser->user;
                $players = collect();
                foreach ($peerUser->squads as $squad) {
                    // Get main player stats using player_id and main_player_match_id
                    $fixture = PlayerMatch::find($squad->main_player_match_id);


                    $mainStats = \App\Models\PlayerStatistic::where('player_id', $squad->main_player_id)
                        ->where('fixture_id', $fixture->fixture_id)
                        ->first();


                    $fixture = PlayerMatch::find($squad->main_player_match_id);
                    // Get sub player stats using player_id and sub_player_match_id
                    $subStats = \App\Models\PlayerStatistic::where('player_id', $squad->sub_player_id)
                        ->where('fixture_id', $fixture->fixture_id)
                        ->first();

                    $useSub = !$mainStats || !$mainStats->did_play;

                    return $players;

                    // Main player object
                    $players->push([
                        'star' => $squad->star_rating,
                        'type' => 'Main',
                        'name' => optional($squad->mainPlayer)->name,
                        'goals' => $mainStats->goals ?? 0,
                        'assists' => $mainStats->assists ?? 0,
                        'shots' => $mainStats->shots ?? 0,
                        'onTarget' => $mainStats->shots_on_target ?? 0,
                        'crosses' => $mainStats->crosses ?? 0,
                        'tackles' => $mainStats->tackles ?? 0,
                        'saves' => $mainStats->saves ?? 0,
                        'cleanSheet' => $mainStats->clean_sheet ?? 0,
                        'yellowCard' => $mainStats->yellow_cards ?? 0,
                        'redCard' => $mainStats->red_cards ?? 0,
                        'total' => $mainStats->points ?? 0,
                        'did_play' => $mainStats->did_play ?? false,
                        'used' => !$useSub,
                    ]);

                    // Sub player object
                    $players->push([
                        'star' => $squad->star_rating,
                        'type' => 'Sub',
                        'name' => optional($squad->subPlayer)->name,
                        'goals' => $subStats->goals ?? 0,
                        'assists' => $subStats->assists ?? 0,
                        'shots' => $subStats->shots ?? 0,
                        'onTarget' => $subStats->shots_on_target ?? 0,
                        'crosses' => $subStats->crosses ?? 0,
                        'tackles' => $subStats->tackles ?? 0,
                        'saves' => $subStats->saves ?? 0,
                        'cleanSheet' => $subStats->clean_sheet ?? 0,
                        'yellowCard' => $subStats->yellow_cards ?? 0,
                        'redCard' => $subStats->red_cards ?? 0,
                        'total' => $subStats->points ?? 0,
                        'did_play' => $subStats->did_play ?? false,
                        'used' => $useSub,
                    ]);
                }

                return [
                    'id' => $user->id,
                    'username' => $user->username,
                    'avatar' => $user->avatar,
                    'email' => $user->email,
                    'created_at' => $user->created_at,
                    'players' => $players,
                ];
            });

            return $this->respondWithCustomData([
                'peer' => $peer,
                'users' => $users,
            ], 200);
        } catch (Exception $e) {
            // dd($e);
            FacadesLog::info($e->getMessage());
            return $this->responseWithErrorMessage('An error occurred while fetching peer details', 500);
        }
    }
}
