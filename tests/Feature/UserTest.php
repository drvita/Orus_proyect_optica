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
    public function test_user_update_by_sales()
    {
        // $this->withoutExceptionHandling();
        $user = User::role('ventas')->inRandomOrder()->first();
        $this->actingAs($user);

        $res = $this->json('PUT', 'api/users/' . $user->id, [
            "branch_id" => rand(0, 1) ? 12 : 13,
            "name" => $user->name,
            "username" => $user->username,
        ]);
        //dd($res->decodeResponseJson());
        $res->assertStatus(200);
    }
}
