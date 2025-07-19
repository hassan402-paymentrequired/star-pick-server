<?php

namespace App\Utils\Service\V1\Peer;

use App\Enum\PeerShareRatioEnum;
use App\Models\Peer;
use App\Utils\Helper\HelperService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Enum\CacheKey;
use App\Exceptions\ClientErrorException;
use App\Models\PeerUser;
use App\Models\PeerUserSquad;
use App\Models\Player;
use App\Models\PlayerMatch;
use App\Models\PlayerMatchStatistics;
use Illuminate\Support\Facades\DB;

class PeerService
{
    public function getPeers(): array
    {
        $page = request('page', 1);
        $ttl = now()->addHours(4);

        $peers = Cache::remember(CacheKey::PEERS->value . "_page_{$page}", $ttl, function () use ($page) {
            return Peer::with('created_by')->withCount('users')->paginate(10, ['*'], 'page', $page);
        });

        $recentPeers = Cache::remember(CacheKey::RECENT_PEERS->value, $ttl, function () {
            return Peer::with('created_by')->latest()->limit(5)->get();
        });

        return [
            'peers' => $peers,
            'recents' => $recentPeers,
        ];
    }


    public function createPeer(Request $request): JsonResponse|Peer
    {
        $peer = Peer::where('user_id', Auth::id())->latest()->first();
        if ($peer) {
            if ($peer->users()->count() === 0) {
                return HelperService::returnWithError('You already have a peer with no users');
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
        return $peer;
    }

    public function updatePeer(Request $request, Peer $peer): void
    {
        $peer->update($request->all());
    }

    public function deletePeer(Peer $peer): void
    {
        $peer->delete();
    }

    public function playBet(Request $request, Peer $peer): void
    {

        if ($peer->users()->count() ===  $peer->limit) {
            throw new ClientErrorException('You cannot join this peer, it has reached its limit');
        }

        $peer->addUser(Auth::id());

        foreach ($request->peers as $value) {
            PlayerMatchStatistics::create([
                'player_id' => $value['main'],
                'peer_id' => $peer->id,
                'sub_player_id' => $value['sub'],
                'rate' => $value['rate'],
            ]);
        }
    }



    public function join(Request $request, Peer $peer)
    {
        $user = auth('api')->user();

        // Check if peer is open
        if ($peer->status !== 'open') {
            return response()->json(['message' => 'This peer is not open for joining.'], 403);
        }

        // Check if user already joined
        if ($peer->users()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'You have already joined this peer.'], 403);
        }

        // Validate squad
        $validated = $request->validate([
            'squad' => 'required|array|size:5',
            'squad.*.star' => 'required|integer|between:1,5',
            'squad.*.main_player_id' => 'required|exists:players,id',
            'squad.*.sub_player_id' => 'required|exists:players,id|different:squad.*.main_player_id',
        ]);

        DB::beginTransaction();

        try {
            // Create peer_user
            $peerUser = PeerUser::create([
                'peer_id' => $peer->id,
                'user_id' => $user->id,
                'total_points' => 0,
                'is_winner' => false
            ]);

            // Create squad
            foreach ($validated['squad'] as $item) {
                PeerUserSquad::create([
                    'peer_user_id' => $peerUser->id,
                    'star_rating' => $item['star'],
                    'main_player_id' => $item['main_player_id'],
                    'sub_player_id' => $item['sub_player_id'],
                ]);
            }

            DB::commit();

            return response()->json(['message' => 'Successfully joined the peer.'], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Something went wrong', 'error' => $e->getMessage()], 500);
        }
    }

   
}
