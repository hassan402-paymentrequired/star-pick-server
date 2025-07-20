<?php

use App\Http\Controllers\V1\Player\PlayerController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:admin'])->group(function () {
    Route::prefix('players')->group(function () {
        Route::get('/', [PlayerController::class, 'index']);
        Route::get('/{player}', [PlayerController::class, 'show']);
        Route::patch('/star/{player}/update', [PlayerController::class, 'updatePlayerStar']);
        Route::get('/{player}', [PlayerController::class, 'show']);
        Route::post('/refetch', [\App\Http\Controllers\V1\Player\PlayerController::class, 'refetch']);


    });

    Route::prefix('peers')->group(function () {
        Route::get('/', [\App\Http\Controllers\V1\Peer\PeerController::class, 'index']);
    });

    Route::prefix('teams')->group(function () {
        Route::get('/', [\App\Http\Controllers\V1\Team\TeamController::class, 'index']);
        Route::get('/{team}/players', [PlayerController::class, 'teamPlayers']);
        Route::post('/refetch', [\App\Http\Controllers\V1\Team\TeamController::class, 'refetch']);

    });

    Route::prefix('match')->group(function () {
        Route::get('/', [\App\Http\Controllers\V1\Match\MatchController::class, 'index']);
        Route::post('/{team}/{league}', [PlayerController::class, 'createMatch']);
    });

    Route::prefix('countries')->group(function () {
        Route::get('/', [\App\Http\Controllers\V1\Country\CountryController::class, 'index']);
        Route::get('/refetch', [\App\Http\Controllers\V1\Country\CountryController::class, 'refetch']);
    });

    Route::prefix('leagues')->group(function () {
        Route::get('/', [\App\Http\Controllers\V1\Leagues\LeagueController::class, 'index']);
        Route::post('/refetch', [\App\Http\Controllers\V1\Leagues\LeagueController::class, 'refetch']);
    });
});
