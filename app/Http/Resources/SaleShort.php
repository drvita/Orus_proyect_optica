<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
//use App\Http\Resources\Payment;

class SaleShort extends JsonResource
{

    public function toArray($request)
    {
        $return = [];

        if (isset($this->id)) {
            $payments = 0;
            $discount = $this->descuento ? $this->descuento : 0;

            foreach ($this->payments as $pay) {
                $payments += $pay->total;
            }

            $return['id'] = $this->id;
            $return['session'] = $this->session;
            $return['subtotal'] = $this->subtotal;
            $return['descuento'] = $discount;
            $return['total'] = $this->subtotal - $discount;
            $return['payments'] = $payments;
        }

        return $return;
    }
}
