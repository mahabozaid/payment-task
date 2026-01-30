<?php

namespace Modules\Auth\Services;

use App\Exceptions\LogicalException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Modules\Auth\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    public function register(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        return User::create($data);
    }

    public function login(array $credentials): string
    {   
        if (!$token = JWTAuth::attempt($credentials)) {

            throw new LogicalException('Invalid credentials');
        }

        return $token;
    }

   public function refreshToken(): array
   {
        $token = JWTAuth::getToken();

        if (! $token) {dd('e');
            throw new JWTException('Token not provided');
        }

        $newToken = JWTAuth::refresh($token);

        return [
            'token' => $newToken,
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
        ];
    }

    public function logout():bool
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return true;
    }
}
