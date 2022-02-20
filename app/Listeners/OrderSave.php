<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Messenger;
use App\Models\StoreItem;
use App\Notifications\OrderNotification;
use App\User;
use Barryvdh\Debugbar\Facade as Debugbar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use App\Mail\orderEmail;
use Illuminate\Support\Facades\Log;

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
        $order = $event->order;
        $udStatus = $event->udStatus;
        $items = $order->items;
        $auth = Auth::user();
        $branchOrder = $order->branch_id ? $order->branch_id : $auth->branch_id;

        // Updated the sale if the order is created or updated
        if (is_array($items) && count($items)) {
            $total = 0;
            // Create total of sale
            foreach ($items as $item) {
                $total += $item['subtotal'];
            }

            if (!$total) {
                Log::warning("La orden '$order->id' no genero venta: $total");
                Messenger::create([
                    "table" => "orders",
                    "idRow" => $order->id,
                    "message" => `No se creo la venta! por total cero.`,
                    "user_id" => 1
                ]);
            }

            $sale = Sale::where('order_id', $order->id)->first();
            // The sales exist for this order?
            if ($sale && $sale->id) {
                $sale->session = $order->session;
                $sale->subtotal = $total;
                $sale->total = $total;
                $sale->updated_at = $order->updated_at;
                $sale->save();
                if ($udStatus) {
                    Messenger::create([
                        "table" => "orders",
                        "idRow" => $order->id,
                        "message" => $auth->name . " actualizo la orden.",
                        "user_id" => 1
                    ]);
                }
            } else {
                // Create sales new
                $A_sale['subtotal'] = $total;
                $A_sale['total'] = $total;
                $A_sale['session'] = $order->session;
                $A_sale['contact_id'] = $order->contact_id;
                $A_sale['order_id'] = $order->id;
                $A_sale['user_id'] = $auth->id;
                $A_sale['branch_id'] = $branchOrder;
                $A_sale['created_at'] = $order->created_at;
                $A_sale['updated_at'] = $order->updated_at;
                Sale::create($A_sale);
                Messenger::create([
                    "table" => "orders",
                    "idRow" => $order->id,
                    "message" => "Cree una orden de venta",
                    "user_id" => 1
                ]);
                // Log::debug("$auth->username, created a sale");
            }

            // only is the order is new or status zero
            if ($order->status === 0) {
                // Delete items of session and create news
                if ($order->session) SaleItem::where('session', $order->session)->delete();

                foreach ($items as $item) {
                    $itemData = StoreItem::where("id", $item['store_items_id'])->first();
                    $branch = $item["branch_id"];

                    if ($itemData && $itemData->id) {
                        if ($itemData->branch_default) {
                            $branch = $itemData->branch_default;
                        }

                        $i_save['cant'] = $item['cant'];
                        $i_save['price'] = $item['price'];
                        $i_save['subtotal'] = $item['subtotal'];
                        $i_save['inStorage'] = $item['inStorage'];
                        $i_save['out'] = isset($item['out']) ? $item['out'] : 0;
                        $i_save['session'] = $order->session;
                        $i_save['store_items_id'] = $item['store_items_id'];
                        $i_save['descripcion'] = isset($item['descripcion']) ? $item['descripcion'] : null;
                        $i_save['branch_id'] = $branch;
                        $i_save['user_id'] = $auth->id;
                        $i_save['created_at'] = $order->created_at;
                        $i_save['updated_at'] = $order->updated_at;

                        SaleItem::create($i_save);
                        Log::debug("New item sale create $itemData->code in branch: $branch for $auth->username");
                    } else {
                        // send notification to admins because someone buy anything and this not exist
                        Log::error("New item sale $itemData->code in branch: $branch for $auth->username not found in database");
                        Messenger::create([
                            "table" => "orders",
                            "idRow" => $order->id,
                            "message" => `Se intento vender {$item['cant']} productos del codigo: {$item['code']} y no puede agregarlos a la venta`,
                            "user_id" => 1
                        ]);
                    }
                }
            }
        } else {
            Log::warning("$auth->username create order but without items");
            Messenger::create([
                "table" => "orders",
                "idRow" => $order->id,
                "message" => `Se creo una orden sin articulos de venta`,
                "user_id" => 1
            ]);
        }
    }
}