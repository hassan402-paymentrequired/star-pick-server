<?php

use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\Peer\PeerController;
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

    // Peer routes
    Route::get('/peers', [PeerController::class, 'index'])->name('peers.index');
    Route::get('/peers/create', [PeerController::class, 'create'])->name('peers.create');
    Route::post('/peers', [PeerController::class, 'store'])->name('peers.store');
    Route::get('/peers/{peer}', [PeerController::class, 'show'])->name('peers.show');
});

Route::get('/verify-otp', [AuthenticationController::class, 'otpIndex'])->name('verify');
Route::post('/verify-otp', [AuthenticationController::class, 'verifyOtp'])->name('verify.store');
