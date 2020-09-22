<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;

class UpdateSale
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
        $items = json_decode($order->items, true);
        
         if(count($items)){
            $total = 0;
            foreach($items as $item){
                $total += $item['total'];
            }

            $sale = Sale::where('order_id', $order->id)->first();
            
            if(is_object($sale) && $sale->id){
                $sale->subtotal = $total;
                $sale->total = $total;
                $sale->updated_at = $order->updated_at;
                $sale->save();
            } else {
                $A_sale['subtotal'] = $total;
                $A_sale['total'] = $total;
                $A_sale['contact_id'] = $order->contact_id;
                $A_sale['order_id'] = $order->id;
                $A_sale['user_id'] = Auth::id();
                $A_sale['created_at'] = $order->created_at;
                $A_sale['updated_at'] = $order->updated_at;
                Sale::create($A_sale);
            }
        }
            
    }
}
