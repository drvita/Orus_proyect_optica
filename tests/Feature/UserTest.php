<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_update_user()
    {
        // $this->withoutExceptionHandling();
        $user = User::role('ventas')->inRandomOrder()->first();
        $this->actingAs($user);

        $res = $this->json('PUT', 'api/users/' . $user->id, [
            "branch_id" => 12,
            "name" => $user->name,
            "username" => $user->username,
        ]);
        dd($res->decodeResponseJson());
        $res->assertStatus(200);
    }
}
