<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Order;
use App\Models\StoreItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;
use App\User;

class StoreItemTest extends TestCase
{
    /**
     * Test about get item by code
     */
    public function test_get_item_code()
    {
        $user = User::role('admin')->inRandomOrder()->first();
        $item = StoreItem::has('inBranch')->inRandomOrder()->first();
        $this->actingAs($user);

        $res = $this->json('GET', 'api/store', [
            "search" => "6506-gray",
        ]);
        //dd($res->decodeResponseJson());
        $res->assertStatus(200);
    }
    /**
     * Test about create items by LIST
     */
    public function test_update_store_by_list()
    {
        // $this->withoutExceptionHandling();
        $user = User::role('admin')->inRandomOrder()->first();
        $item = StoreItem::has('inBranch')->inRandomOrder()->first();
        // $branch = $item->inBranch()->inRandomOrder()->first();
        $price = rand(100, 1000);
        $cost = $price - ((rand(10, 35) / 100) * $price);
        $invoice_num = Str::random(8);

        $this->actingAs($user);
        $items = array(
            array(
                'code' => 'codecode',
                'codebar' => '',
                'category_id' => 55,
                'supplier_id' => 868,
                'brand_id' => 110,
                'branch_id' => 12,
                'cant' => rand(1, 5),
                'name' => 'Armazon acetato optifree 10ml code',
                "price" => $price,
                "cost" => $cost,
                "invoice" => $invoice_num,
            ),
            array(
                'code' => $item->code,
                'codebar' => $item->codebar,
                'category_id' => 54,
                'supplier_id' => 1392,
                'brand_id' => 114,
                'branch_id' => 13,
                'cant' => rand(1, 5),
                'name' => $item->name,
                "price" => rand(100, 1000),
                "invoice" => $invoice_num,
            ),
            array(
                'code' => Str::random(6),
                'codebar' => Str::random(22),
                'category_id' => 176,
                'supplier_id' => 1583,
                'brand_id' => 103,
                'branch_id' => rand(12, 13),
                'cant' => rand(1, 5),
                'name' => 'Armazon armazon de prueba JIMMY CHOO mgtrer',
                "price" => rand(100, 1000),
                "invoice" => $invoice_num,
            ),
        );
        $res = $this->json('POST', 'api/store/bylist', [
            "items" => $items
        ]);

        if ($res->status() != 200) {
            //dd($res->decodeResponseJson());
        }
        //dd($res->decodeResponseJson());
        $res->assertStatus(200)
            ->assertJson([
                "status" => "ok"
            ]);

        foreach ($items as $item) {
            $rows = StoreItem::where("code", $item['code'])->get();

            if (!$rows) {
                $rows = StoreItem::where("codebar", $item['codebar'])->first();
            }

            if (!$rows->count()) {
                //dd($item, $rows->toArray());
            }
            $this->assertTrue(true);

            $row = $rows[0];

            $this->assertTrue($item['code'] == $row['code']);
            if ($item['code'] != $row['code']) {
                //dd("code", $item, $row->toArray());
            }

            $this->assertTrue($item['codebar'] == $row['codebar']);
            if ($item['codebar'] != $row['codebar']) {
                //dd("codebar", $item, $row->toArray());
            }

            $branch_id = $row->branch_default ? $row->branch_default : $item['branch_id'];
            $branch = $row->inBranch()->where("branch_id", $branch_id)->first();

            if (!$branch) {
                //dd($item, $rows->toArray());
            }

            if ($item['price'] != $branch->price) {
                //dd("price", $item, $branch->toArray());
            }
            $this->assertTrue($item['price'] == $branch->price);

            if ($item['cant'] > $branch->cant) {
                //dd("cant", $item, $branch->toArray());
            }
            $this->assertTrue($item['cant'] <= $branch->cant);
        }
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
        //dd($res->decodeResponseJson());
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
        //dd($res->decodeResponseJson());
        $res->assertStatus(200);
    }
}
