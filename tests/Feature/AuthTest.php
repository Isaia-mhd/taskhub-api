<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/v1/register', [
            'name' => 'Mohamed',
            'email' => 'mohamed@example.com',
            'password' => '@Password123',
            'password_confirmation' => '@Password123',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'email' => 'mohamed@example.com',
        ]);
    }

    public function test_user_can_login(): void
    {
        User::factory()->create([
            'email' => 'mohamed@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this
            ->withHeader('Origin', 'http://localhost:5173')
            ->postJson('/api/v1/login', [
                'email' => 'mohamed@example.com',
                'password' => 'password123',
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data' => [
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
                'token',
                'token_type',
            ],
        ]);
        $response->assertJson([
            'data' => [
                'token_type' => 'Bearer',
            ],
        ]);
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'mohamed@example.com',
            'password' => Hash::make('@Password123'),
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'mohamed@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_authenticated_user_can_get_his_profile(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this
            ->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/user');

        $response->assertStatus(200);

        $response->assertJson([
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
            ],
        ]);
    }

    public function test_guest_cannot_get_user_profile(): void
    {
        $response = $this->getJson('/api/v1/user');

        $response->assertStatus(401);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this
            ->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/logout');

        $response->assertStatus(200);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_complete_authentication_flow(): void
    {
        $user = User::factory()->create([
            'email' => 'mohamed@example.com',
            'password' => Hash::make('@Password123'),
        ]);

        // Login
        $loginResponse = $this->postJson('/api/v1/login', [
            'email' => 'mohamed@example.com',
            'password' => '@Password123',
        ]);

        $loginResponse->assertStatus(200);
        $token = $loginResponse->json('data.token');
        $this->assertIsString($token);

        // Access authenticated endpoint
        $userResponse = $this
            ->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/user');

        $userResponse->assertStatus(200);

        $userResponse->assertJson([
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
            ],
        ]);

        // Logout
        $logoutResponse = $this
            ->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/logout');

        $logoutResponse->assertStatus(200);

        $this->assertDatabaseCount('personal_access_tokens', 0);
        $this->app['auth']->forgetGuards();

        $loggedOutResponse = $this
            ->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/user');

        $loggedOutResponse->assertStatus(401);
    }
}
