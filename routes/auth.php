<?php

use Illuminate\Support\Facades\Route;

Route::post('/login', [\App\Http\Controllers\V1\Auth\SessionController::class, 'authenticate'])
    ->name('auth.login');

Route::post('/register', [\App\Http\Controllers\V1\Auth\RegisterUserController::class, 'register'])
    ->name('auth.register');

Route::middleware(['auth:api'])->group(function () {
    Route::post('/logout', [\App\Http\Controllers\V1\Auth\SessionController::class, 'logout'])
        ->name('auth.logout');
    Route::post('/setup-username', [\App\Http\Controllers\V1\Auth\RegisterUserController::class, 'setupUsername'])
        ->name('auth.setup-username');

    Route::patch('/edit-profile', [\App\Http\Controllers\V1\Auth\ProfileController::class, 'editProfile'])
        ->name('auth.user.edit-profile');

    Route::patch('/edit-password', [\App\Http\Controllers\V1\Auth\ProfileController::class, 'editPassword'])
        ->name('auth.user.edit-password');
});

Route::post('/admin/login', [\App\Http\Controllers\V1\Auth\AdminAuthController::class, 'login'])
    ->name('admin.login');
