<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Messenger;
use Illuminate\Support\Facades\Auth;

class PaymentSave
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
        $payment = $event->payment;
        $idMessege = $event->idMessege;
        $table = $event->table;
        Messenger::create([
            "table" => $table,
            "idRow" => $idMessege,
            "message" => Auth::user()->name . " abono a la cuenta ($ " . $payment->total . ")",
            "user_id" => 1
        ]);
    }
}