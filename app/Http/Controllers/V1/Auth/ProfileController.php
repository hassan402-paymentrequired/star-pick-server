<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function editProfile(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|string|max:255',
        ]);
        $user = AuthUser();
        $user->update($request->all());
        return $this->respondWithCustomData([
            'message' => 'profile updated successfully'
        ], 200);
    }

    public function editPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
            'old_password' => 'required|string|min:8',
        ]);

        if (!Hash::check($request->old_password, AuthUser()->password)) {
            return $this->responseWithErrorMessage('old password does not match', ['password' => 'old password does not match']);
        }
        $user = AuthUser();
        $user->update([
            'password' => Hash::make($request->password)
        ]);
        return $this->respondWithCustomData([
            'message' => 'password updated successfully'
        ], 200);
    }
}
