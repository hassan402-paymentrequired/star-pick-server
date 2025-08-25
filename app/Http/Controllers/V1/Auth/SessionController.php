<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Utils\Service\V1\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function authenticate(LoginRequest $request): JsonResponse
    {
        $token = $this->authService->login($request->email, $request->password);
        if (!$token) {
            return $this->responseWithErrorMessage('Invalid credentials', ['email' => 'invalid credentials'], 401);
        }
        $user = Auth::guard('api')->user();
        $user->load('wallet');
        return $this->respondWithCustomData([
            'token' => $token,
            'user' => $user,
            'meassage' => 'login successful'
        ], 200);
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout();
        return $this->respondWithCustomData([
            'message' => 'logout successfully'
        ], 200);
    }
}
