<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\SaleItem;
use App\Models\Messenger;
use App\Models\StoreItem;
use App\Models\Payment;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SaleSave
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $auth = Auth::user();
        $sale = $event->sale;
        $items = $sale->items;
        $branch_id = $sale->branch_id ? $sale->branch_id : $auth->branch_id;
        $session = $sale->session;

        $subtotal = 0;

        //delete
        if (!$auth) {
            $auth = User::where("id", 2)->first();
        }

        if (count($items)) {
            foreach ($items as $item) {
                $subtotal += $item["total"];
                $branch = $branch_id;
                $itemData = StoreItem::where("id", $item['store_items_id'])->first();
                $saleItem = SaleItem::where("id", $item["id"])->first();

                if ($saleItem) {
                    $saleItem->cant = $item['cant'];
                    $saleItem->price = $item['price'];
                    $saleItem->subtotal = $item['total'];
                    $saleItem->store_items_id = $item['store_items_id'];
                    $saleItem->descripcion = isset($item['descripcion']) ? $item['descripcion'] : null;
                    $saleItem->save();
                } else {
                    if ($itemData && $item["total"]) {
                        if ($itemData->branch_default) {
                            $branch = $itemData->branch_default;
                        }

                        $i_save['cant'] = $item['cant'];
                        $i_save['price'] = $item['price'];
                        $i_save['subtotal'] = $item["total"];
                        $i_save['session'] = $session;
                        $i_save['store_items_id'] = $item['store_items_id'];
                        $i_save['descripcion'] = isset($item['descripcion']) ? $item['descripcion'] : null;
                        $i_save['branch_id'] = $branch;
                        $i_save['user_id'] = $auth->id;

                        SaleItem::create($i_save);
                    }
                }
            }

            if (!$subtotal) {
                Log::warning("La venta '$sale->id' no genero venta.");
                return Messenger::create([
                    "table" => "orders",
                    "idRow" => $sale->id,
                    "message" => "No se creo la venta! por total cero.",
                    "user_id" => 1
                ]);
            }
        }

        if (isset($sale["paymentsRequest"])) {
            $amount = 0;
            $payments = isset($sale["paymentsRequest"]) ? $sale["paymentsRequest"] : [];
            $paymentsDb = Payment::where('sale_id', $sale->id)->get()->filter(function ($pay) use ($payments) {
                foreach ($payments as $payment) {
                    if (isset($payment["id"]) && $payment["id"] == $pay->id) {
                        // $pay->metodopago = $payment['metodopago'];
                        // $pay->details = isset($payment['details']) ?  $payment['details'] : "";
                        // $pay->auth = isset($payment['auth']) ? $payment['auth'] : "";
                        // $pay->total = $payment['total'];
                        // $pay->bank_id = isset($payment['bank_id']) ? $payment['bank_id'] : null;
                        // $pay->save();
                        return true;
                    }
                }
                return false;
            })->pluck('id');

            Payment::where('sale_id', $sale->id)->whereNotIn("id", $paymentsDb)->get()->each(function ($pay) use ($auth) {
                $pay->deleted_at = Carbon::now();
                $pay->updated_id = $auth->id;
                $pay->save();
            });

            foreach ($payments as $payment) {
                $amount += $payment['total'];

                if (preg_match("/^new\d{10,}/im", $payment["id"])) {
                    $sale->payments()->create([
                        "metodopago" => $payment['metodopago'],
                        "details" => $payment['details'] ?? "",
                        "auth" => $payment['auth'] ?? "",
                        "total" => $payment['total'],
                        "bank_id" => $payment['bank_id'] ?? null,
                        "contact_id" => $sale->contact_id,
                        "branch_id" => $branch_id,
                        "user_id" => $auth->id,
                    ]);
                }
            }

            unset($sale["items"]);
            unset($sale["payments"]);
            unset($sale["addPayments"]);
            unset($sale["method"]);
            unset($sale["paymentsRequest"]);
            $sale->pagado = $amount;
            $sale->save();
        } else if ($sale->method === "update" && $sale->addPayments) {
            $sale->payments()->get()->each(function ($item) use ($auth) {
                $item->deleted_at = Carbon::now();
                $item->updated_id = $auth->id;
                $item->save();
            });
        }
    }
}
