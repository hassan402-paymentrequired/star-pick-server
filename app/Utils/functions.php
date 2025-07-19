<?php

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

function getUserBalance()
{
    return Auth::guard('api')->user()->wallet->balance;
}

function decreaseWallet($amount)
{
    AuthUser()->wallet->balance -= $amount;
    AuthUser()->wallet->save();
}
function increaseWallet($amount)
{
   AuthUser()->wallet->balance += $amount;
   AuthUser()->wallet->save();
}

function AuthUser(string $guard = 'api'): User|Admin
{
    return Auth::guard($guard)->user();
}