<?php

namespace App\Utils\Service\V1\Auth;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function login(string $emailorphone, $password): string|null
    {
        $fieldType = filter_var($emailorphone, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $user = User::where($fieldType, $emailorphone)->first();

        if (!$user) {
            return false;
        }
        if (!Hash::check($password, $user->password)) {
            return null;
        }
        $token = Auth::guard('api')->login($user);
        return $token;
    }

    public function register(Request $request): string|null
    {
        $user = User::create([
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
        ]);
        return Auth::guard('api')->login($user);
    }


    public function setUsername(Request $request): void
    {
        // $uploadFolder = 'uploads/avatars';
        $user = Auth::user();
        // if ($image = $request->file('image')) {
        //     $image_uploaded_path = $image->store($uploadFolder, 'public');
        // }
        $user->username = $request->username;
        $user->avatar = $request->avatar;
        $user->save();
    }

    public function adminLogin(Request $request): string|null
    {
        $admin = Admin::where('email', $request->email)->first();
        if (!Hash::check($request->password, $admin->password)) {
            return null;
        }
        return Auth::guard('admin')->login($admin);
    }

    public function logout(): void
    {
        Auth::logout();
    }
}
