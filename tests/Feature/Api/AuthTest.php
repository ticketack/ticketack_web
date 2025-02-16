<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test user login with valid credentials.
     */
    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
            'device_name' => 'test_device',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'email_verified_at',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    /**
     * Test user cannot login with invalid credentials.
     */
    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'wrong_password',
            'device_name' => 'test_device',
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test authenticated user can get their profile.
     */
    public function test_user_can_get_their_profile(): void
    {
        $user = User::factory()->create();
        
        $token = $user->createToken('test_device')->plainTextToken;

        $response = $this->getJson('/api/auth/user', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJson($user->toArray());
    }

    /**
     * Test user can logout.
     */
    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        
        $token = $user->createToken('test_device')->plainTextToken;

        $response = $this->postJson('/api/auth/logout', [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logged out successfully']);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    /**
     * Test unauthenticated user cannot access protected routes.
     */
    public function test_unauthenticated_user_cannot_access_protected_routes(): void
    {
        $response = $this->getJson('/api/auth/user');

        $response->assertStatus(401);
    }
}
