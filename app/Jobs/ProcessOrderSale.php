<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use App\Models\Sale;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessOrderSale
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;
    protected $saleData;
    protected $auth;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order, $saleData, $user)
    {
        $this->order = $order;
        $this->saleData = $saleData;
        $this->auth = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $order = $this->order;
        $saleData = $this->saleData;
        $auth = $this->auth;

        if (!$order->sale) {
            Log::warning("[ProcessOrder.Sale] Sale not found for order: " . $order->id);
            return;
        }

        Log::info("[ProcessOrder.Sale] Processing sale for order: " . $order->id);
        DB::transaction(function () use ($order, $saleData, $auth) {
            $sale = $order->sale;

            // 1. Update Discount checking if it exists in saleData
            if (isset($saleData['discount'])) {
                $sale->descuento = $saleData['discount'];
                $sale->save();
                Log::info("[ProcessOrder.Sale] Updated discount for sale: " . $sale->id);
            }

            // 2. Process Payments
            if (isset($saleData['payments'])) {
                $payments = $saleData['payments'];
                Payment::where('sale_id', $sale->id)->delete();

                $amount = 0;
                $branch_id = $order->branch_id;

                foreach ($payments as $payment) {
                    $amount += $payment['total'];
                    $sale->payments()->create([
                        "metodopago" => $payment['metodopago'], // There aren't default value
                        "details" => $payment['details'] ?? "",
                        "auth" => (!empty($payment['auth']) ? $payment['auth'] : null),
                        "total" => $payment['total'] ?? 0,
                        "bank_id" => (!empty($payment['bank_id']) ? $payment['bank_id'] : null),
                        "contact_id" => $sale->contact_id,
                        "branch_id" => $branch_id,
                        "user_id" => $auth->id ?? 1,
                    ]);
                }

                $sale->pagado = $amount;
                $sale->save();
                Log::info("[ProcessOrderSale] Updated payments for Sale " . $sale->id . ". Total Paid: " . $amount);
            }
        });
    }
}
