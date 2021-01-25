<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class orderEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $data = array();

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $id)
    {
        $this->data['name'] = $name;
        $this->data['id'] = $id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Pedido en Óptica Madero')
                ->view('email.order');
    }
}
