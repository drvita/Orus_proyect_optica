<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Messenger;
use App\Models\StoreItem;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class OrderSave
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $auth = Auth::user();
        $order = $event->order;
        $updateStatus = $event->udStatus;
        $session = $order->session;
        $items = $order->items;
        $branch_id = $order->branch_id;
        $sale = $order->sale ? $order->sale : new \stdClass;
        $discount = $sale && isset($sale['discount']) ? $sale['discount'] : 0;
        $payments = $sale && isset($sale['payments']) ? $sale['payments'] : [];
        $subtotal = 0;

        SaleItem::where('session', $session)->get()->each(function ($item) {
            $item->delete();
        });

        foreach ($items as $item) {
            $subtotal += $item["total"];
            $branch = $branch_id;
            $lots = null;
            $cant = $item['cant'];

            $itemData = StoreItem::where("id", $item['store_items_id'])->first();
            if ($itemData->branch_default) {
                $branch = $itemData->branch_default;
            }

            $i_save['cant'] = $cant;
            $i_save['price'] = $item['price'];
            $i_save['subtotal'] = $item["total"];
            $i_save['session'] = $session;
            $i_save['descripcion'] = isset($item['descripcion']) ? $item['descripcion'] : null;
            $i_save['store_items_id'] = $item['store_items_id'];
            $i_save['branch_id'] = $branch;
            $i_save['user_id'] = $auth->id;

            $branchItem = $itemData->inBranch()->where("branch_id", $branch)->first();
            if ($branchItem) {
                $i_save['store_branch_id'] = $branchItem->id;
                $lots = $branchItem->lots()->orderBy("created_at")->get();
            }

            if (isset($lots) && $lots->count()) {
                foreach ($lots as $lot) {
                    $i_save['store_lot_id'] = $lot->id;
                    if ($lot->cant >= $cant) {
                        SaleItem::create($i_save);
                        break;
                    } else {
                        $cant -= $lot->cant;
                        $i_save['cant'] = $lot->cant;
                        SaleItem::create($i_save);
                    }
                }
            } else {
                SaleItem::create($i_save);
            }
        }

        if (!$subtotal) {
            Log::warning("La orden '$order->id' no genero venta.");
            return Messenger::create([
                "table" => "orders",
                "idRow" => $order->id,
                "message" => "No se creo la venta! por total cero.",
                "user_id" => 1
            ]);
        }

        if (!$updateStatus) {
            $sale = [];

            $sale['session'] = $order->session;
            $sale['subtotal'] = $subtotal;
            $sale['descuento'] = $discount;
            $sale['total'] = $subtotal - $discount;
            $sale['contact_id'] = $order->contact_id;
            $sale['order_id'] = $order->id;
            $sale['user_id'] = $auth->id;
            $sale['branch_id'] = $branch_id;
            $sale = Sale::create($sale);

            Messenger::create([
                "table" => "orders",
                "idRow" => $order->id,
                "message" => "Cree una nueva venta",
                "user_id" => 1
            ]);

            if ($sale && count($payments)) {
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
                        "details" => isset($payment['details']) && $payment['details'] ? $payment['details'] : "",
                        "auth" => $payment['auth'],
                        "total" => $payment['total'],
                        "bank_id" => isset($payment['bank_id']) && $payment['bank_id'] ? $payment['bank_id'] : null,
                        "contact_id" => $sale->contact_id,
                        "branch_id" => $branch_id,
                        "user_id" => $auth->id,
                    ]);
                }

                $sale->pagado = $amount;
                $sale->save();
            }
        }
    }
}
