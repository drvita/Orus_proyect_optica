<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Order;
use App\Http\Resources\ContactShort;
use App\Http\Resources\SaleItemShort;
//use App\Http\Resources\Payment;

class SaleInContact extends JsonResource
{

    public function toArray($request)
    {
        $return = [];

        if (isset($this->id)) {
            $return['id'] = $this->id;
            $return['session'] = $this->session;
            $return['total'] = $this->total;
            $return['pedido'] = $this->order_id;
            $return['branch'] = new ConfigBranch($this->branch);
            $return['created_at'] = $this->created_at ? $this->created_at->format('Y-m-d H:i') : null;
        }

        return $return;
    }
}
