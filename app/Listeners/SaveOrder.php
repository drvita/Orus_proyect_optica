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

class SaveOrder
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

        // Updated the sale if the order is created or updated
        if (is_array($items) && count($items)) {
            $total = 0;
            // Create total of sale
            foreach ($items as $item) {
                $total += $item['subtotal'];
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
                        "message" => Auth::user()->name . " actualizo la orden.",
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
                $A_sale['user_id'] = Auth::user()->id;
                $A_sale['branch_id'] = $order->branch_id;
                $A_sale['created_at'] = $order->created_at;
                $A_sale['updated_at'] = $order->updated_at;
                Sale::create($A_sale);
                Messenger::create([
                    "table" => "orders",
                    "idRow" => $order->id,
                    "message" => "Cree una orden de venta",
                    "user_id" => 1
                ]);
            }

            // only is the order is new or status zero
            if ($order->status === 0) {
                // Delete items of session and create news
                if ($order->session) {
                    $articulos = SaleItem::where('session', $order->session)->get();
                    foreach ($articulos as $articulo) {
                        SaleItem::find($articulo->id)->delete();
                    }
                }

                foreach ($items as $item) {
                    $itemData = StoreItem::where("id", $item['store_items_id'])->first();
                    $branch = $order->branch_id;

                    if (isset($item['branch_id']) && $item['branch_id']) {
                        $branch = $item['branch_id'];
                    }

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
                        $i_save['user_id'] = Auth::user()->id;
                        $i_save['created_at'] = $order->created_at;
                        $i_save['updated_at'] = $order->updated_at;

                        SaleItem::create($i_save);
                        Log::debug('Create item save');
                    } else {
                        // send notification to admins because someone buy anything and this not exist
                        Messenger::create([
                            "table" => "orders",
                            "idRow" => $order->id,
                            "message" => `Se intento vender {$item['cant']} productos del codigo: {$item['code']} y no puede agregarlos a la venta`,
                            "user_id" => 1
                        ]);
                    }
                }

                // User::all()
                //     ->except(1)
                //     ->except(Auth::user()->id)
                //     ->where("rol", 0)
                //     ->each(function (User $user) use ($order) {
                //         Notification::send($user, new OrderNotification($order));
                //     });
            }
            // else if ($order->status === 3) {
            //     // Debugbar::info($order->paciente->name .":". $order->id);
            //     if ($order->paciente->email && !preg_match('/.+@domain.com$/', $order->paciente->email)) {
            //         Mail::to($order->paciente->email)->send(new orderEmail($order->paciente->name, $order->id));
            //         Messenger::create([
            //             "table" => "orders",
            //             "idRow" => $order->id,
            //             "message" => "Se envio notificación por correo electronico a: " . $order->paciente->email,
            //             "user_id" => 1
            //         ]);
            //     } else {
            //         Messenger::create([
            //             "table" => "orders",
            //             "idRow" => $order->id,
            //             "message" => "No pude enviar un correo electronico de notificación por que no tiene asignado uno",
            //             "user_id" => 1
            //         ]);
            //     }
            // }
            // else if ($order->status === 3) {
            //     // Debugbar::info($order->paciente->name .":". $order->id);
            //     if ($order->paciente->email && !preg_match('/.+@domain.com$/', $order->paciente->email)) {
            //         // Mail::to($order->paciente->email)->send(new orderEmail($order->paciente->name, $order->id));
            //         Messenger::create([
            //             "table" => "orders",
            //             "idRow" => $order->id,
            //             "message" => "Se envio notificación por correo electronico a: " . $order->paciente->email,
            //             "user_id" => 1
            //         ]);
            //     } else {
            //         Messenger::create([
            //             "table" => "orders",
            //             "idRow" => $order->id,
            //             "message" => "No pude enviar un correo electronico de notificación por que no tiene asignado uno",
            //             "user_id" => 1
            //         ]);
            //     }
            // }
        } else {
            // Order created without sales
            Messenger::create([
                "table" => "orders",
                "idRow" => $order->id,
                "message" => `Se creo una orden sin articulos de venta`,
                "user_id" => 1
            ]);
        }
    }
}