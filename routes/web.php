<?php

use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\Peer\PeerController;
use App\Http\Controllers\Wallet\WalletController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticationController::class, 'login'])->name('login');
    Route::post('/login', [AuthenticationController::class, 'storeLogin'])->name('login.store');
    Route::get('/sign-up', [AuthenticationController::class, 'register'])->name('register');
    Route::post('/sign-up', [AuthenticationController::class, 'storeRegister'])->name('register.store');
});


Route::middleware(['auth:web', 'verified'])->group(function () {
    Route::get('/', [PeerController::class, 'index'])->name('home');

    Route::prefix('peers')->group(function () {
        Route::get('/', [PeerController::class, 'index'])->name('peers.index');
        Route::get('/contents', [PeerController::class, 'contents'])->name('peers.contents');
        Route::get('/chanllenged', [PeerController::class, 'globalPeer'])->name('peers.global');
        Route::get('/create', [PeerController::class, 'create'])->name('peers.create');
        Route::post('/', [PeerController::class, 'store'])->name('peers.store');
        Route::get('/{peer}', [PeerController::class, 'show'])->name('peers.show');
        Route::get('/join/{peer}', [PeerController::class, 'joinPeer'])->name('peers.join');
        Route::post('/join/{peer}', [PeerController::class, 'storeJoinPeer'])->name('peers.join.store');
    });
    Route::prefix('wallet')->group(function () {
        Route::get('/', [WalletController::class, 'index'])->name('wallet.index');
    });
});

Route::get('/verify-otp', [AuthenticationController::class, 'otpIndex'])->name('verify');
Route::post('/verify-otp', [AuthenticationController::class, 'verifyOtp'])->name('verify.store');
