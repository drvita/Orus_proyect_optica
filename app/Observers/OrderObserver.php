<?php

namespace App\Observers;

use App\Models\Order;
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
        $order->status = 0;
        $order->session = Str::uuid();

        if (Auth::check()) {
            $user = Auth::user();
            $order->user_id = $user->id;
            $order->branch_id = $user->branch_id;
        }
    }
}
