<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class Sale extends JsonResource{

    public function toArray($request){
        $return['id'] = $this->id;
        $return['session'] = $this->session;
        $return['subtotal'] = $this->subtotal;
        $return['descuento'] = $this->descuento ? $this->descuento : 0 ;
        $return['total'] = $this->total;
        $return['items'] = SaleItemShort::collection($this->items);
        $return['customer'] = new ContactSimple($this->cliente);
        $return['pedido'] = $this->order_id;
        $return['payments'] = Payment::collection($this->payments);
        $return['created'] = new UserInExam($this->user);
        $return['created_at'] = $this->created_at->format('Y-m-d H:i');
        $return['updated_at'] = $this->updated_at->format('Y-m-d H:i');
        return $return;
    }
}
