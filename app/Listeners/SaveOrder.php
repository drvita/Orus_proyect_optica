<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Messenger;
use Illuminate\Support\Facades\Auth;

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
        $items = json_decode($order->items, true);

        //Actualiza la venta si la orden es creada o modificada
        if(is_array($items) && count($items)){
            $total = 0;
            foreach($items as $item){
                $total += $item['subtotal'];
            }
            
            $sale = Sale::where('order_id', $order->id)->first();
            
            if(is_object($sale) && $sale->id){
                $sale->session = $order->session;
                $sale->subtotal = $total;
                $sale->total = $total;
                $sale->updated_at = $order->updated_at;
                $sale->save();
                if($udStatus){
                    Messenger::create([
                        "table" => "orders",
                        "idRow" => $order->id,
                        "message" => Auth::user()->name ." actualizo la orden.",
                        "user_id" => 1
                    ]);
                }
            } else {
                $A_sale['subtotal'] = $total;
                $A_sale['total'] = $total;
                $A_sale['session'] = $order->session;
                $A_sale['contact_id'] = $order->contact_id;
                $A_sale['order_id'] = $order->id;
                $A_sale['user_id'] = Auth::user()->id;
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
        }
        //Actualiza los items de la orden y venta
        if($order->status === 0){
            if($order->session){
                $articulos = SaleItem::where('session', $order->session)->get();
                foreach($articulos as $articulo){
                    SaleItem::find($articulo->id)->delete();
                }

            }

            if($items && count($items)){
                foreach($items as $item){
                    $i_save['cant'] = $item['cant'];
                    $i_save['price'] = $item['price'];
                    $i_save['subtotal'] = $item['subtotal'];
                    $i_save['inStorage'] = $item['inStorage'];
                    $i_save['out'] = isset($item['out']) ? $item['out'] : 0;
                    $i_save['session'] = $order->session;
                    $i_save['store_items_id'] = $item['store_items_id'];
                    $i_save['descripcion'] = isset($item['descripcion']) ? $item['descripcion'] : null;
                    $i_save['user_id'] = Auth::user()->id;
                    $i_save['created_at'] = $order->created_at;
                    $i_save['updated_at'] = $order->updated_at;
                    SaleItem::create($i_save);
                }
                
            }
        }
    }
}
