<?php

namespace Tests\Feature;

use App\Models\Config;
use App\Models\Contact;
use App\Models\Order;
use App\Models\StoreItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;
use App\Models\User;

class StoreItemTest extends TestCase
{
    public function test_update_store_by_list()
    {
        // $this->withoutExceptionHandling();
        $user = User::role('admin')->inRandomOrder()->first();
        // $item = StoreItem::has('inBranch')->inRandomOrder()->first();
        // $branch = $item->inBranch()->inRandomOrder()->first();
        $price = rand(100, 1000);
        $cost = $price - ((rand(10, 35) / 100) * $price);
        $invoice_num = Str::random(8);

        $this->actingAs($user);
        $items = array(
            array(
                'code' => 'codecode',
                'codeBar' => '',
                'category_id' => 55,
                'supplier_id' => 868,
                'brand_id' => 110,
                'branch_id' => 15,
                'cant' => 2,
                'name' => 'Armazon acetato optifree 10ml code',
                "price" => $price,
                "cost" => $cost,
                "invoice" => $invoice_num,
            ),
            array(
                'code' => 'rm32523',
                'codeBar' => '',
                'category_id' => 54,
                'supplier_id' => 1392,
                'brand_id' => 114,
                'branch_id' => 13,
                'cant' => 2,
                'name' => 'Armazon metalico perry ellis rm32523',
                "price" => $price,
                "cost" => $cost,
                "invoice" => $invoice_num,
            ),
            array(
                'code' => 'mgtrer',
                'codeBar' => '',
                'category_id' => 176,
                'supplier_id' => 1583,
                'brand_id' => 103,
                'branch_id' => 12,
                'cant' => 2,
                'name' => 'Armazon armazon de prueba JIMMY CHOO mgtrer',
                "price" => $price,
                "invoice" => $invoice_num,
            ),
        );
        $res = $this->json('POST', 'api/store/bylist', [
            "items" => $items
        ]);
        dd($res->decodeResponseJson());
        $res->assertStatus(200);
    }
    public function test_create_order()
    {
        // $this->withoutExceptionHandling();
        $user = User::role('admin')->inRandomOrder()->first();
        $item = StoreItem::has("inBranch")->inRandomOrder()->first();
        $branchItem = $item->inBranch()->first();
        $contact = Contact::has("exams")->inRandomOrder()->first();
        $exam = $contact->exams()->first();

        $data = [
            "session" => Str::random(36),
            "contact_id" => $contact->id,
            "exam_id" => $exam->id,
            "items" => [
                [
                    "store_items_id" => $item->id,
                    "cant" => 1,
                    "price" => $branchItem->price,
                ]
            ],
        ];

        $this->actingAs($user);
        $res = $this->json('POST', 'api/orders', $data);
        // dd($res->decodeResponseJson());
        $res->assertStatus(200);
    }
    public function test_show_order()
    {
        // $this->withoutExceptionHandling();
        $user = User::role('admin')->inRandomOrder()->first();
        $order = Order::whereHas('items', function ($q) {
            $q->has('lot');
        })->inRandomOrder()->first();


        $this->actingAs($user);
        $res = $this->json('GET', 'api/orders/' . $order->id);
        dd($res->decodeResponseJson());
        $res->assertStatus(200);
    }
}
