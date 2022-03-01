<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Order;
use App\Http\Resources\ContactShort;
use App\Http\Resources\SaleItemShort;
//use App\Http\Resources\Payment;

class SaleInPayment extends JsonResource
{

    public function toArray($request)
    {
        $return = [];

        if (isset($this->id)) {
            $return['id'] = $this->id;
            $return['customer'] = new ContactSimple($this->cliente);
            $return['session'] = $this->session;
            $return['subtotal'] = $this->subtotal;
            $return['total'] = $this->total;
            $return['pedido'] = $this->order_id;
            $return['created'] = new UserInExam($this->user);
            $return['created_at'] = $this->created_at->format('Y-m-d H:i');
        }

        return $return;
    }
}