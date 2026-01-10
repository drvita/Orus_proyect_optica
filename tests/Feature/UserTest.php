<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
        $this->seed();
    }

    /**
     * Test the users.data endpoint returns authenticated user data
     *
     * @return void
     */
    public function test_users_data_returns_authenticated_user_data()
    {
        // Create a test user
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Authenticate the user with Sanctum
        Sanctum::actingAs($user);

        // Make the request to the users.data endpoint
        $response = $this->getJson(route('users.data'));

        // Assert the response is successful
        $response->assertStatus(200);
    }
}
