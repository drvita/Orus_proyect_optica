<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\SaleItem;
use Illuminate\Support\Facades\Auth;

class SaveSale
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $sale = $event->sale;
        if(!$sale->order_id){
            $items = json_decode($sale->items, true);
            if($sale->session){
                SaleItem::where('session', $sale->session)->delete();
            }
            if(count($items)){
                foreach($items as $item){
                    $i_save['cant'] = $item['cant'];
                    $i_save['price'] = $item['price'];
                    $i_save['subtotal'] = $item['subtotal'];
                    $i_save['inStorage'] = $item['inStorage'];
                    $i_save['out'] = isset($item['out']) ? $item['out'] : 0;
                    $i_save['session'] = $sale->session;
                    $i_save['store_items_id'] = $item['store_items_id'];
                    $i_save['descripcion'] = $item['descripcion'];
                    $i_save['user_id'] = Auth::user()->id;
                    $i_save['created_at'] = $sale->created_at;
                    $i_save['updated_at'] = $sale->updated_at;
                    SaleItem::create($i_save);
                }
            }
        }
    }
}
