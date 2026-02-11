<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ProcessOrderItemsUpdate
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
        if (is_string($items)) {
            $items = json_decode($items, true);
        }
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
        $reqItems = collect($this->items);
        $dbItems = $order->items->keyBy('id');

        $missingItems = $reqItems->filter(function ($item) use ($dbItems) {
            return !isset($item['id']) || !$dbItems->has($item['id']);
        });

        if ($missingItems->isNotEmpty()) {
            Log::critical("[ProcessOrderItemsUpdate] Critical Error: Items validation failed. Some items not found in order " . $order->id, [
                'missing_items' => $missingItems->toArray(),
                'request_items' => $reqItems->toArray()
            ]);
            return;
        }

        $reqItems->each(function ($item) use ($dbItems) {
            $dbItem = $dbItems->get($item['id']);
            $updateData = [];
            if (isset($item['out'])) $updateData['out'] = $item['out'];
            if (isset($item['inStorage'])) $updateData['inStorage'] = $item['inStorage'];

            if (!empty($updateData)) {
                $dbItem->update($updateData);
            }
        });

        Log::info("[ProcessOrderItemsUpdate] Items updated successfully for order " . $order->id);
    }
}
