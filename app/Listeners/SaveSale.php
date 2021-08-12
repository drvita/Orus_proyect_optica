<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\SaleItem;
use App\Models\Payment;
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
        $items = json_decode($sale->items ? $sale->items : "[]", true);
        $payments = json_decode($sale->payments ? $sale->payments : "[]", true);

        if(count($items)){
            if($sale->session){
                SaleItem::where('session', $sale->session)->delete();

                foreach($items as $item){
                    $i_save['cant'] = $item['cant'];
                    $i_save['price'] = $item['price'];
                    $i_save['subtotal'] = $item['subtotal'];
                    $i_save['inStorage'] = $item['inStorage'];
                    $i_save['out'] = isset($item['out']) ? $item['out'] : 0;
                    $i_save['session'] = $sale->session;
                    $i_save['store_items_id'] = $item['store_items_id'];
                    $i_save['descripcion'] = isset($item['descripcion']) ? $item['descripcion'] : "";
                    $i_save['user_id'] = Auth::user()->id;

                    SaleItem::create($i_save);
                }
            }
            
        }
        if(count($payments)){
            
            foreach($payments as $payment){
                Payment::updateOrCreate(
                    ['id' => $payment['id']],
                    [
                        "metodopago" => $payment['metodopago'],
                        "details" => $payment['details'],
                        "auth" => $payment['auth'],
                        "total" => $payment['total'],
                        "bank_id" => $payment['bank_id'],
                        "sale_id" => $sale->id,
                        "contact_id" => $sale->contact_id,
                        "user_id" => Auth::user()->id,
                    ]
                );
            }
        }
    }
}
