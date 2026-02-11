<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use App\Models\SaleItem;
use App\Models\StoreItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessOrderItems
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;
    protected $items;
    protected $auth;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order, $items, User $auth)
    {
        $this->order = $order;
        $this->items = $items;
        $this->auth = $auth;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $order = $this->order;
        $items = collect($this->items);
        $auth = $this->auth;

        if ($items->isEmpty()) {
            Log::info("[ProcessOrder.Items] No items to process for order: " . $order->id);
            return;
        }

        Log::info("[ProcessOrder.Items] Processing " . $items->count() . " items for order: " . $order->id);
        DB::transaction(function () use ($order, $items, $auth) {
            $session = $order->session;
            $branch_id = $order->branch_id;

            // 1. Clean previous items
            $this->cleanSessionItems($session);

            // 2. Process Order Items
            $subtotal = $this->processOrderItems($items, $session, $branch_id, $auth);

            // 3. Update Sale
            $sale = $order->sale;
            if ($sale) {
                $sale->update([
                    "subtotal" => $subtotal,
                ]);
                Log::info("[ProcessOrder.Items] Updated Sale " . $sale->id . " Subtotal: " . $subtotal);
            } else {
                Log::error("[ProcessOrder.Items] Sale not found for order: " . $order->id);
                $order->createSale([
                    "subtotal" => $subtotal,
                ]);
            }
        });
    }

    private function cleanSessionItems($session)
    {
        Log::info("[ProcessOrder.Items] Clean session: " . $session);
        SaleItem::where('session', $session)->get()->each(function ($item) {
            $item->delete();
        });
    }

    private function processOrderItems($items, $session, $branch_id, $auth)
    {
        $subtotal = 0;

        // Optimization: Eager load Store Items with Branches and Lots
        $storeItemsIds = $items->pluck('store_items_id')->unique();
        $storeItems = StoreItem::whereIn('id', $storeItemsIds)->get()->keyBy('id');

        foreach ($items as $item) {
            $item = (object) $item; // Ensure it's an object for consistent access
            $store_items_id = $item->store_items_id;
            $itemData = $storeItems->get($store_items_id);
            if (!$itemData) {
                continue;
            }

            $cant = $item->cant;
            $price = $item->price;
            $descripcion = $item->descripcion ?? null;
            $subtotal += $cant * $price;

            $i_save = [
                'cant' => $cant,
                'price' => $price,
                'subtotal' => $cant * $price,
                'session' => $session,
                'descripcion' => $descripcion,
                'store_items_id' => $store_items_id,
                'branch_id' => $branch_id,
                'user_id' => $auth ? $auth->id : 1,
            ];

            SaleItem::create($i_save);
            Log::info("[ProcessOrder.Items] Sale-Item created");
        }

        return $subtotal;
    }
}
