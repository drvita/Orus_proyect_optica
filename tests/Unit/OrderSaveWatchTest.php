<?php

namespace Tests\Unit;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use App\Listeners\SaleSave;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderSaveWatchTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_payments()
    {
        if (!Auth::check()) {
            // Auth::loginUsingId(2);
        }

        $request = new Request([
            "id" => 5486,
            "contact_id" => 1341,
            "discount" => 111,
            "items" => [
                [
                    "id" => 28567,
                    "cant" => 2,
                    "price" => 1050,
                    "store_items_id" => 12238,
                ],
                [
                    "id" => 28568,
                    "cant" => 1,
                    "price" => 11,
                    "store_items_id" => 13217,
                ],
            ],
            "payments" => [
                [
                    "id" => 7988,
                    "metodopago" => 1,
                    "total" => 100,
                ],
                [
                    "id" => 7989,
                    "metodopago" => 2,
                    "total" => 50,
                    "auth" => "4526",
                    "bank_id" => 10
                ],
                [
                    "id" => "new235434656456",
                    "metodopago" => 1,
                    "total" => 200,
                ],
            ]
        ]);
        $sale = Sale::where("id", $request->id)->first();
        $sale['items'] = getItemsRequest($request->items, $sale->branch_id);;
        $sale['paymentsRequest'] = getPaymentsRequest($request->payments, $request['branch_id']);

        $listener = new SaleSave();
        $listener->handle((object) [
            "sale" => $sale,
            "udStatus" => "update"
        ]);

        $this->assertTrue(true);
    }
}
