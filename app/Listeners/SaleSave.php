<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\SaleItem;
use App\Models\Sale;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
        $sale = $event->sale;
        $items = $sale->items;

        if (count($items)) {
            if ($sale->session) {
                SaleItem::where('session', $sale->session)->get()->each(function ($item) {
                    $item->delete();
                });


                foreach ($items as $item) {
                    $i_save['cant'] = $item['cant'];
                    $i_save['price'] = $item['price'];
                    $i_save['subtotal'] = $item['subtotal'];
                    $i_save['inStorage'] = $item['inStorage'];
                    $i_save['out'] = isset($item['out']) ? $item['out'] : 0;
                    $i_save['session'] = $sale->session;
                    $i_save['store_items_id'] = $item['store_items_id'];
                    $i_save['descripcion'] = isset($item['descripcion']) ? $item['descripcion'] : "";
                    $i_save['user_id'] = Auth::user()->id;
                    $i_save['branch_id'] = $sale->branch_id;

                    SaleItem::create($i_save);
                }

                // dd(SaleItem::where('session', $sale->session)->get()->toArray());
            }
        }

        if (isset($sale["payment_status"])) {
            $payments = $sale->payments;
            Payment::where('sale_id', $sale->id)->get()->each(function ($item) {
                $item->deleted_at = Carbon::now();
                $item->save();
            });

            foreach ($payments as $payment) {
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
                        "branch_id" => $sale->branch_id,
                        "user_id" => Auth::user()->id,
                        "deleted_at" => null,
                    ]
                );
            }
        }

        //dd("SAle", Sale::find($sale->id)->relations()->get()->toArray());
    }
}
