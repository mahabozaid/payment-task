<?php

namespace Modules\Auth\Http\Controllers;

use App\Utils\ApiResponse;
use Illuminate\Routing\Controller;
use Modules\Auth\Http\Requests\LoginRequest;
use Modules\Auth\Http\Requests\RegisterRequest;
use Modules\Auth\Services\AuthService;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        $user = $this->authService->register($request->validated());

        return ApiResponse::success('User created successfully',$user, code: 201, httpStatus: 201);
    }

    public function login(LoginRequest $request)
    {
        $token = $this->authService->login($request->validated());

        return ApiResponse::success('User logged in successfully', ['token' => $token]);
    }

    public function refreshToken()
    {
        $data = $this->authService->refreshToken();

        return ApiResponse::success('Token refreshed successfully', $data);
    }

    public function logout()
    {
        $this->authService->logout();

        return ApiResponse::success('User logged out successfully');
    }
}
