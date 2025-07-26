<?php

use App\Http\Controllers\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    Route::prefix('auth')->group(
        base_path('routes/auth.php')
    );

    Route::prefix('admin')->group(
        base_path('routes/admin.php')
    );

    Route::get('/football', [\App\Http\Controllers\V1\Player\PlayerController::class, 'football']);

    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        Route::prefix('players')->group(function () {
            Route::get('/', [\App\Http\Controllers\V1\Player\PlayerController::class, 'index']);
            Route::post('/', [\App\Http\Controllers\V1\Player\PlayerController::class, 'store']);
            Route::get('/{player}', [\App\Http\Controllers\V1\Player\PlayerController::class, 'show']);
            Route::patch('/{player}', [\App\Http\Controllers\V1\Player\PlayerController::class, 'update']);
            Route::delete('/{player}', [\App\Http\Controllers\V1\Player\PlayerController::class, 'destroy']);
        });


        Route::prefix('peers')->group(function () {
            Route::get('/', [\App\Http\Controllers\V1\Peer\PeerController::class, 'index']);
            // Route::get('/peer-users', [\App\Http\Controllers\V1\Peer\PeerController::class, 'index']);
            Route::post('/', [\App\Http\Controllers\V1\Peer\PeerController::class, 'store']);
            Route::post('/{peer}/join-peer', [\App\Http\Controllers\V1\Peer\PeerController::class, 'joinPeer']);
            Route::post('/{peer}/leave-peer', [\App\Http\Controllers\V1\Peer\PeerController::class, 'leavePeer']);
            Route::get('/{id}', [\App\Http\Controllers\V1\Peer\PeerController::class, 'show']);
            Route::patch('/{peer}', [\App\Http\Controllers\V1\Peer\PeerController::class, 'update']);
            Route::delete('/{peer}', [\App\Http\Controllers\V1\Peer\PeerController::class, 'destroy']);
            Route::get('/my-peers/ongoing', [\App\Http\Controllers\V1\Peer\PeerController::class, 'myOngoingPeers']);
            Route::get('/my-peers/completed', [\App\Http\Controllers\V1\Peer\PeerController::class, 'myCompletedPeers']);
        });

        Route::prefix('match')->group(function () {
            Route::get('/', [\App\Http\Controllers\V1\Match\MatchController::class, 'index']);
            Route::get('/group-by-star', [\App\Http\Controllers\V1\Player\PlayerController::class, 'getPlayersByStar']);
        });

        Route::prefix('payment')->group(function () {
            Route::post('/initialize', [\App\Http\Controllers\V1\Payment\PaymentController::class, 'initialize'])->name('paystack.initialize');
            Route::get('/callback', [\App\Http\Controllers\V1\Payment\PaymentController::class, 'callback'])->name('paystack.callback');
            Route::get('/cancel', [\App\Http\Controllers\V1\Payment\PaymentController::class, 'cancel'])->name('paystack.cancel');
            Route::post('/deposit', [\App\Http\Controllers\V1\Payment\PaymentController::class, 'increaseWalletBalance'])->name('paystack.cancel');
        });

        Route::prefix('general')->group(function(){
            // Route::get();
        });


    });

    Route::prefix('sofa')->group(function () {
        Route::get('/fetch/countries', [TestController::class, 'index']);
    });
});


