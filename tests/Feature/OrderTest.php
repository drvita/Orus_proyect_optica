<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_save_order()
    {
        // $this->withoutExceptionHandling();
        $user = User::role('admin')->inRandomOrder()->first();
        $session = Str::random(8) . "." . Str::random(8) . "." . Str::random(8);

        $this->actingAs($user);
        $data = array(
            'session' => $session,
            'contact_id' => 390,
            'items' =>
            array(
                0 =>
                array(
                    'cant' => 2,
                    'price' => 56,
                    'subtotal' => 112,
                    'inStorage' => true,
                    'session' => $session,
                    'out' => 0,
                    'descripcion' => '',
                    'store_items_id' => 10224,
                ),
                1 =>
                array(
                    'cant' => 1,
                    'price' => 953,
                    'subtotal' => 953,
                    'inStorage' => true,
                    'session' => $session,
                    'out' => 0,
                    'descripcion' => '',
                    'store_items_id' => 13230,
                ),
            ),
            'sale' =>
            array(
                'discount' => 106,
                'payments' =>
                array(
                    0 =>
                    array(
                        'metodopago' => 1,
                        'total' => 100,
                        'bank_id' => NULL,
                        'details' => NULL,
                        'auth' => '',
                    ),
                ),
            ),
            'exam_id' => 2406,
        );
        $res = $this->json('POST', 'api/orders', $data);
        dd($res->decodeResponseJson());
        $res->assertStatus(200);
    }
}
