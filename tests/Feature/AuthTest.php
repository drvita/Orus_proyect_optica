<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
        $this->seed();
    }

    /**
    * Test successful login with valid credentials.
    */
    public function test_user_can_login_with_valid_credentials(): void
    {
        // Create a user
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Attempt to login
        $response = $this->postJson(route('users.login'), [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        // Assert the response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'status',
                'data'
            ]);
    }

    /**
    * Test login failure with invalid credentials.
    */
    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        // Create a user
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Attempt to login with wrong password
        $response = $this->postJson(route('users.login'), [
            'email' => 'test@example.com',
            'password' => 'wrong_password',
        ]);

        // Assert the response
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'La contraseña es incorrecta',
            ]);
    }

    /**
    * Test user logout functionality.
    */
    public function test_user_can_logout(): void
    {
        // Create and authenticate a user
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Attempt to logout
        $response = $this->postJson(route('users.logout'));

        // Assert the response
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logged out successfully',
            ]);

        // Assert that the token count is zero (all tokens revoked)
        $this->assertCount(0, $user->tokens);
    }
}

