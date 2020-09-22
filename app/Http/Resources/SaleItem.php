<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleItem extends JsonResource{

    public function toArray($request){
        $return['id'] = $this->id;
        $return['producto'] = $this->item->name;
        $return['almacen'] = $this->item->cant;
        $return['cantidad'] = $this->cant;
        $return['precio'] = $this->price;
        $return['subtotal'] = $this->subtotal;
        $return['pedido'] = $this->inStorage;
        $return['session'] = $this->session;
        $return['created'] = $this->user->name ? $this->user->name : "";
        $return['created_at'] = $this->created_at->diffForHumans();
        $return['updated_at'] = $this->updated_at->diffForHumans();
        return $return;
    }
}
