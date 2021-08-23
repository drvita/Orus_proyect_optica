<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Order;
use App\Http\Resources\ContactShort;
use App\Http\Resources\SaleItemShort;

class SaleInOrder extends JsonResource{

    public function toArray($request){
        $return = [];

        if(isset($this->id)){
            $return['id'] = $this->id;
            $return['subtotal'] = $this->subtotal;
            $return['total'] = $this->total;
            $return['items'] = SaleItemShort::collection($this->items);
            $return['paid'] = $this->pagado;
            $return['payments'] = PaymentInOrder::collection($this->payments);
            $return['created'] = new UserInExam($this->user);
            $return['created_at'] = $this->created_at->format('Y-m-d H:i');
        }
        
        return $return;
    }
}
