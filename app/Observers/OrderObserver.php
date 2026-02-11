<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
// use Illuminate\Support\Facades\Log;

class OrderObserver
{
    /**
     * Handle the Order "creating" event.
     */
    public function creating(Order $order): void
    {
        $order->status = Order::STATUS_PENDING;
        $order->session = (string) Str::uuid();

        if (Auth::check()) {
            $user = Auth::user();
            $order->user_id = $user->id;
            $order->branch_id = $user->branch_id;
        }
    }

    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        $order->createSale();
    }

    /**
     * Handle the Order "updating" event.
     */
    public function updating(Order $order): void
    {
        if ($order->isDirty('status') && $order->status == Order::STATUS_DELIVERED) {
            $order->delivered_at = now();
        }
    }
}
