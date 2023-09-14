<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SalesTest extends TestCase
{
    /**
     * Test update sale
     *
     * @return void
     */
    public function test_update_sale()
    {
        $this->withoutExceptionHandling();
        $user = User::role('ventas')->inRandomOrder()->first();

        $res = $this->actingAs($user)
            ->putJson('/api/sales/9935', [
                "id" => 9935,
                "contact_id" => 8577,
                "discount" => 0,
                "items" => [
                    [
                        "id" => 47387,
                        "cant" => 2,
                        "price" => 650,
                        "store_items_id" => 10421
                    ],
                    [
                        "id" => 47388,
                        "cant" => 1,
                        "price" => 3920,
                        "store_items_id" => 15797
                    ]
                ],
                "payments" => [
                    [
                        "id" => "new1694706732756",
                        "metodopago" => 2,
                        "total" => 220,
                        "auth" => "1212",
                        "bank_id" => 0
                    ]
                ],
            ]);
        dd($res->getContent());
        $res->assertStatus(200);
    }
}