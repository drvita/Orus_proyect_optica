<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Order;
use App\Http\Resources\ContactShort;
use App\Http\Resources\SaleItemShort;

class Sale extends JsonResource{

    public function toArray($request){
        $return['id'] = $this->id;
        $return['session'] = $this->session;
        $return['subtotal'] = $this->subtotal;
        $return['descuento'] = $this->descuento ? $this->descuento : 0 ;
        $return['total'] = $this->total;
        $return['productos'] = is_object($this->items) 
            ? SaleItemShort::collection($this->items) 
            : [];
        $return['cliente'] = new ContactShort($this->cliente);
        $return['pedido'] = $this->order_id;
        $return['pagado'] = $this->abonos[0]->suma ? $this->abonos[0]->suma : 0;
        $return['created'] = $this->user->name;
        $return['created_at'] = $this->created_at->format('Y-m-d H:i');
        $return['updated_at'] = $this->updated_at->format('Y-m-d H:i');
        return $return;
    }
}
