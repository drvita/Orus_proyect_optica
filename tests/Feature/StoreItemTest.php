<?php

namespace Tests\Feature;

use App\Models\Config;
use App\Models\Contact;
use App\Models\StoreItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;
use App\User;

class StoreItemTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_update_store_by_list()
    {
        // $this->withoutExceptionHandling();
        $user = User::role('admin')->inRandomOrder()->first();
        $item = StoreItem::has('inBranch')->inRandomOrder()->first();
        $branch = $item->inBranch()->inRandomOrder()->first();
        $price = rand(100, 1000);
        $cost = $price - ((rand(10, 35) / 100) * $price);

        $this->actingAs($user);
        $res = $this->json('POST', 'api/store/bylist', [
            "items" => [
                [
                    "id" => $item->id,
                    "branch_id" => $branch->branch_id,
                    "cant" => rand(1, 10),
                    "price" => $price,
                    "cost" => $cost,
                    "invoice" => Str::random(8),
                ],
            ]
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
        dd($res->decodeResponseJson());
        $res->assertStatus(200);
    }
}
