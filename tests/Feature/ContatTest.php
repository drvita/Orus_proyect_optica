<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContatTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_get_suppliers()
    {
        // $this->withoutExceptionHandling();
        $user = User::role('admin')->inRandomOrder()->first();
        $this->actingAs($user);

        $res = $this->json('GET', 'api/contacts', [
            "type" => 1,
            "business" => 0
        ]);
        dd($res->decodeResponseJson());
        $res->assertStatus(200);
    }
}
