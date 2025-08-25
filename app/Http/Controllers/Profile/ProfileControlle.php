<?php

namespace App\Http\Controllers\Profile;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class ProfileControlle extends \App\Http\Controllers\Controller
{

    public function index()
    {
        return Inertia::render('profile/index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'phone' => [
                'required',
                'string',
                'digits:11',
                Rule::unique(User::class)->ignore(AuthUser('web')->id),
            ],
        ]);
        AuthUser('web')->fill($request->only(['phone', 'username']));
        $request->user()->save();
        return to_route('profile.index')->with('success', 'profile updated successfully');
    }
}
