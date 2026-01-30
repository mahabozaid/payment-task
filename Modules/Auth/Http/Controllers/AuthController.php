<?php

namespace Modules\Auth\Http\Controllers;

use App\Utils\ApiResponse;
use Modules\Products\Models\Product;
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
        $this->mockCreateTestProducts(); //only for testing
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

    private function mockCreateTestProducts()
    {
        if(Product::count() > 0){
            return;
        }

        for($i=1;$i<5;$i++){
            Product::create([
                'name' => 'Product '.$i,
                'description' => 'Description '.$i,
                'price' => 100
            ]);
        }
    }
}
