<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleItemShort extends JsonResource{

    public function toArray($request){
        $return = [];
        if(isset($this->id)){
            $return['id'] = $this->id;
            $return['store_items_id'] = $this->item->id;
            $return['producto'] = $this->item->name;
            $return['cantidad'] = $this->cant;
            $return['precio'] = $this->price;
            $return['subtotal'] = $this->subtotal;
            $return['inStorage'] = $this->inStorage ? true : false;
            $return['descripcion'] = $this->descripcion;
        }
        return $return;
    }
}
