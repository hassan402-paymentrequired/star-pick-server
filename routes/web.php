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


Route::middleware('auth:web')->group(function () {
    Route::get('/', [PeerController::class, 'index'])->name('home');
});
