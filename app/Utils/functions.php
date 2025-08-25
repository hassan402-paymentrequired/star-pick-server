<?php

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

const ADMIN = 'admin';
const API = 'api';
const WEB = 'web';

function getUserBalance($guard = 'api')
{
    return Auth::guard($guard)->user()->wallet->balance;
}

function decreaseWallet($amount, $guard = 'api')
{
    AuthUser($guard)->wallet()->decrement('balance', $amount);
}
function increaseWallet($amount, $guard = 'api')
{
     AuthUser($guard)->wallet()->increment('balance', $amount);
}

function AuthUser(string $guard = 'api'): User|Admin
{
    return Auth::guard($guard)->user();
}


function generateOtp($length = 6)
{
    $numbers = range(0, 9);
    shuffle($numbers);

    return implode(array_slice($numbers, 0, $length));
}

function hasEnoughBalance($amount, $guard): bool
{
    return AuthUser($guard)->wallet->balance >= $amount;
}
