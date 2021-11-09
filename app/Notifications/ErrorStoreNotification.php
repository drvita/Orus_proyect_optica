<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ErrorStoreNotification extends Notification
{
    use Queueable;
    private $item;
    private $sale;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($sale, $item)
    {
        $this->item = $item;
        $this->sale = $sale;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $order = $this->sale->order;
        $sale = $this->sale->saleDetails;

        return [
            'item' => $this->item->name,
            'item_branch_default' => $this->item->branch_default,
            'branch_id' => $this->sale->branch_id,
            'cant' => $this->sale->cant,
            'user' => $this->sale->user->name,
            'order_id' => $order ? $order->id : 0,
            'sale_id' => $sale ? $sale->id : 0
        ];
    }
}