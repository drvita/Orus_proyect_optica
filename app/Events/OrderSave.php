<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderSave
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $order;
    public $udStatus;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($order, $udStatus)
    {
        $this->order = $order;
        $this->udStatus = $udStatus;
    }
}