<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Order;
use App\Http\Resources\ContactShort;
use App\Http\Resources\SaleItemShort;
//use App\Http\Resources\Payment;

class SaleInContact extends JsonResource{

    public function toArray($request){
        $return = [];

        if(isset($this->id)){
            $return['id'] = $this->id;
            $return['session'] = $this->session;
            $return['subtotal'] = $this->subtotal;
            $return['descuento'] = $this->descuento ? $this->descuento : 0 ;
            $return['total'] = $this->total;
            $return['productos'] = is_object($this->items) 
                ? SaleItemShort::collection($this->items) 
                : [];
            $return['pedido'] = $this->order_id;
            $return['branch'] = new Config($this->branch);
            $return['created'] = new UserInExam($this->user);
            $return['created_at'] = $this->created_at->format('Y-m-d H:i');
            $return['updated_at'] = $this->updated_at->format('Y-m-d H:i'); 
        }
        
        return $return;
    }
}