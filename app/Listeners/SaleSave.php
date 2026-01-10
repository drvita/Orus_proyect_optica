<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\SaleItem;
use App\Models\Messenger;
use App\Models\StoreItem;
use App\Models\Payment;
use App\Models\User;
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

        if (!$auth) {
            $auth = User::where("id", 2)->first();
        }

        if (count($items)) {
            $itemsFound = SaleItem::where("session", $sale->session)
                ->get()
                ->filter(function ($item) use ($items) {
                    foreach ($items as $i) {
                        if ($i['id'] == $item->id) return true;
                    }

                    return false;
                })->pluck('id');

            SaleItem::where("session", $sale->session)
                ->whereNotIn("id", $itemsFound)
                ->get()
                ->each(function ($item) {
                    $item->delete();
                });

            foreach ($items as $item) {
                $subtotal += $item["total"];
                $branch = $branch_id;
                $itemData = StoreItem::where("id", $item['store_items_id'])->first();
                $saleItem = SaleItem::where("id", $item["id"])->first();
                $cant = $item['cant'];

                if ($saleItem) {
                    $saleItem->cant = $cant;
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
                        $lots = null;

                        $i_save['cant'] = $cant;
                        $i_save['price'] = $item['price'];
                        $i_save['subtotal'] = $item["total"];
                        $i_save['session'] = $session;
                        $i_save['store_items_id'] = $item['store_items_id'];
                        $i_save['descripcion'] = isset($item['descripcion']) ? $item['descripcion'] : null;
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
                        "details" => isset($payment['details']) && $payment['details'] ? $payment['details'] : "",
                        "auth" => $payment['auth'] ?? "",
                        "total" => $payment['total'],
                        "bank_id" => isset($payment['bank_id']) && $payment['bank_id'] ? $payment['bank_id'] : null,
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
