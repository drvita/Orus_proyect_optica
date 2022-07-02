<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\SaleItem;
use App\Models\Messenger;
use App\Models\StoreItem;
use App\Models\Payment;
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
        $payments = isset($sale["paymentsRequest"]) ? $sale["paymentsRequest"] : [];
        $subtotal = 0;

        if (count($items)) {
            SaleItem::where('session', $session)->get()->each(function ($item) {
                $item->delete();
            });

            foreach ($items as $item) {
                $subtotal += $item["total"];
                $branch = $branch_id;
                $itemData = StoreItem::where("id", $item['store_items_id'])->first();


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

        if (count($payments)) {
            $amount = 0;

            Payment::where('sale_id', $sale->id)->get()->each(function ($item) use ($auth) {
                $item->deleted_at = Carbon::now();
                $item->updated_id = $auth->id;
                $item->save();
            });

            foreach ($payments as $payment) {
                $amount += $payment['total'];

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
