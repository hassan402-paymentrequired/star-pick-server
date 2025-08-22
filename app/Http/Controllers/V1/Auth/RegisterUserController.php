<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Utils\Service\V1\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterUserController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $token = $this->authService->register($request);
        $user = Auth::guard('api')->user();
        $user->load('wallet');
        return $this->respondWithCustomData([
            'token' => $token,
            'message' => 'registration successful',
            'user' => $user
        ], 200);
    }

    public function setupUsername(Request $request): JsonResponse
    {
        $request->validate([
            'username' => 'required|string|min:3|max:20|alpha_dash|unique:users',
            'avatar' => 'required|string|url',
        ]);
        $this->authService->setUsername($request);

        return $this->respondWithCustomData([
            'message' => 'username updated successfully',
        ], 200);
    }
}
