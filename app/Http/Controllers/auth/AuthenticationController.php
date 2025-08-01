<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class AuthenticationController extends Controller
{

    public function register()
    {
        return Inertia::render('auth/register');
    }


    public function login()
    {
        return Inertia::render('auth/login');
    }

    public function storeLogin(Request $request)
    {
        $request->validate([
            'phone' => 'required|exists:users,phone|digits:11',
            'password' => 'required|min:8'
        ]);

        $user = User::find($request->phone)->first();

        if (!$user) {
            return back()->with('error', 'Invalid credentials');
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Invalid credentials');
        }

        Auth::login($user);
        return to_route('home');
    }


    function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function storeRegister(Request $request)
    {
        $request->validate([
            'phone' => 'required|digits:11|unique:users,phone',
            'password' => 'required|min:8|confirmed',
            'username' => 'required|string|min:3|max:20|alpha_dash|unique:users',
        ]);

        $user = User::create([
            'phone' => $request->phone,
            'password' => $request->password,
            'username' => $request->username,
            'email' => $request->username . time() . rand(1000, 9999) . '@gmail.com'
        ]);

        Auth::login($user);


        return to_route('home')->with('success', 'Registration successful');
    }
}
