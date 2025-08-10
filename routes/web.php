<?php

use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\Peer\PeerController;
use App\Http\Controllers\Profile\ProfileControlle;
use App\Http\Controllers\Tournament\TournamentController;
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
        Route::get('/create', [PeerController::class, 'create'])->name('peers.create');
        Route::post('/', [PeerController::class, 'store'])->name('peers.store');
        Route::get('/{peer}', [PeerController::class, 'show'])->name('peers.show');
        Route::get('/join/{peer}', [PeerController::class, 'joinPeer'])->name('peers.join');
        Route::post('/join/{peer}', [PeerController::class, 'storeJoinPeer'])->name('peers.join.store');
    });

    Route::prefix('tournament')->group(function () {
        Route::get('/', [TournamentController::class, 'index'])->name('tournament.index');
        Route::get('/join', [TournamentController::class, 'create'])->name('tournament.create');
        Route::post('/join', [TournamentController::class, 'store'])->name('tournament.store');
        Route::get('/{user}', [TournamentController::class, 'show'])->name('tournament.user.show');
    });

    Route::prefix('wallet')->group(function () {
        Route::get('/', [WalletController::class, 'index'])->name('wallet.index');
        Route::get('/details', [WalletController::class, 'getWalletDetails']);
        Route::post('/fund', [WalletController::class, 'initializeFunding']);
        Route::post('/verify-payment', [WalletController::class, 'verifyPayment']);
        Route::get('/transactions', [WalletController::class, 'getTransactionHistory']);
        Route::get('/transactions/{transactionId}', [WalletController::class, 'getTransactionDetails']);
    });

    // Virtual Account Management Routes
    Route::prefix('virtual-accounts')->group(function () {
        Route::get('/details', [\App\Http\Controllers\Customers\VirtualAccountController::class, 'getVirtualAccountDetails']);
        Route::post('/create', [\App\Http\Controllers\Customers\VirtualAccountController::class, 'createVirtualAccount']);
        Route::post('/deactivate', [\App\Http\Controllers\Customers\VirtualAccountController::class, 'deactivateVirtualAccount']);
        Route::get('/transactions', [\App\Http\Controllers\Customers\VirtualAccountController::class, 'getVirtualAccountTransactions']);
    });


    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileControlle::class, 'index'])->name('profile.index');
    });


    Route::prefix('withdrawals')->group(function () {
        Route::post('/', [\App\Http\Controllers\Customers\WithdrawalController::class, 'initiateWithdrawal']);
    });
});


// Webhook Routes (No authentication required)
Route::prefix('webhooks')->group(function () {
    Route::post('/paystack/payment', [\App\Http\Controllers\Customers\WalletController::class, 'processWebhook']);
    Route::post('/paystack/virtual-account', [\App\Http\Controllers\Customers\VirtualAccountController::class, 'processWebhook']);
});





Route::middleware('guest')->group(function () {
    Route::get('/verify-otp', [AuthenticationController::class, 'otpIndex'])->name('verify');
    Route::post('/verify-otp', [AuthenticationController::class, 'verifyOtp'])->name('verify.store');
});
