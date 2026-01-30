<?php

namespace Modules\Auth\tests\Feature;

use Tests\TestCase;
use Modules\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;


class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function register_endpoint_require_valid_data()
    {
        $response = $this->postJson('/api/auth/register', []);

        $response->assertStatus(400);
        $response->assertJson(['success' => false, 'message' => 'The name field is required.']);
    }

    /** @test */
    public function register_must_use_unique_email()
    {
        $userData1 = User::factory()->create()->toArray();
        $userData2 = User::factory()->create()->toArray();
        $userData2['email'] = $userData1['email'];

        $response = $this->postJson('/api/auth/register', $userData2);

        $response->assertStatus(400);
        $response->assertJson(['success' => false, 'message' => 'The email has already been taken.']);
    }

    /** @test */
    public function user_register_successfully()
    {
        $password = bcrypt('password');
        $userData = [
            'name' => 'abozaid',
            'email' => 'abozaid@example.com',
            'password' => $password,
            'password_confirmation' => $password,
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(201);
        $response->assertJson(['success' => true, 'message' => 'User created successfully']);
    }

      /** @test */
    public function it_fail_login_if_user_enter_invalid_credentials()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(400);
        $response->assertJson(['success' => false, 'message' => 'Invalid credentials']);
    }

    /** @test */
    public function it_login_successfully()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'message' => 'User logged in successfully']);
    }


}
