<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;
use App\Utils\Service\V1\Auth\AuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminAuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(AdminLoginRequest $request)
    {
        Log::info('Middleware stack:', [
        'method' => $request->method(),
        'middlewares' => $request->route()->middleware()
    ]);
        $token = $this->authService->adminLogin($request);
        if (!$token) {
            return $this->responseWithErrorMessage('Invalid credentials', ['email' => 'invalid credentials'], 400);
        }
        return $this->respondWithCustomData([
            'token' => $token,
            'user' => Auth::guard('admin')->user(),
            'message' => 'login successful'
        ], 200);
    }
}
