<?php

use App\Http\Controllers\V1\Player\PlayerController;
use App\Http\Controllers\V1\Sofa\SofaController;
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
        Route::post('/refetch', [\App\Http\Controllers\V1\Team\TeamController::class, 'refetch']);
        Route::patch('/{team}/status', [\App\Http\Controllers\V1\Team\TeamController::class, 'updateStatus']);
        Route::get('/{team_id}/players', [\App\Http\Controllers\V1\Team\TeamController::class, 'players']);
    });

    Route::prefix('match')->group(function () {
        Route::get('/', [\App\Http\Controllers\V1\Match\MatchController::class, 'index']);
        Route::post('/create-from-fixture', [\App\Http\Controllers\V1\Match\MatchController::class, 'createFromFixture']);
        Route::post('/refetch-statistics', [\App\Http\Controllers\V1\Match\MatchController::class, 'refetchStatistics']);
        Route::get('/{playerMatch}/statistics', [\App\Http\Controllers\V1\Match\MatchController::class, 'getMatchStatistics']);
    });

    Route::prefix('countries')->group(function () {
        Route::get('/', [\App\Http\Controllers\V1\Country\CountryController::class, 'index']);
        Route::get('/refetch', [\App\Http\Controllers\V1\Country\CountryController::class, 'refetch']);
    });

    Route::prefix('leagues')->group(function () {
        Route::get('/', [\App\Http\Controllers\V1\Leagues\LeagueController::class, 'index']);
        Route::get('/seasons/{league}', [\App\Http\Controllers\V1\Leagues\LeagueController::class, 'getLeagueSeason']);
        Route::get('/season-rounde/{league}', [\App\Http\Controllers\V1\Leagues\LeagueController::class, 'getLeagueSeasonAndRound']);
        Route::post('/refetch', [\App\Http\Controllers\V1\Leagues\LeagueController::class, 'refetch']);
        Route::get('/{league}', [\App\Http\Controllers\V1\Leagues\LeagueController::class, 'show']);
    });

     Route::prefix('seasons')->group(function () {
        Route::get('/', [\App\Http\Controllers\V1\Season\SeasonController::class, 'index']);
        Route::post('/refetch', [\App\Http\Controllers\V1\Leagues\LeagueController::class, 'refetch']);
    });

    Route::prefix('users')->group(function () {
        // Route::get('/', [\App\Http\Controllers\V1\User\UserController::class, 'index']);
    });

    Route::prefix('fixtures')->group(function () {
        Route::get('/', [\App\Http\Controllers\V1\Fixture\FixtureController::class, 'index']);
        Route::post('/refetch', [\App\Http\Controllers\V1\Fixture\FixtureController::class, 'refetch']);
    });

    Route::prefix('sofa')->group(function () {
        Route::post('/countries', [SofaController::class, 'countries']);
        Route::post('/leagues', [SofaController::class, 'leagues']);
        Route::post('/teams', [SofaController::class, 'teams']);
        Route::post('/players', [SofaController::class, 'players']);
        Route::post('/seasons', [SofaController::class, 'seasons']);
        Route::post('/rounds', [SofaController::class, 'rounds']);
    });
});
