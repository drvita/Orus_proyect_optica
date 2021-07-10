<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleItem extends JsonResource{

    public function toArray($request){
        $return = [];
        
        if($this->id){
            $return['id'] = $this->id;
            $return['store_items_id'] = $this->item->id;
            $return['producto'] = $this->item->name;
            $return['cantidad'] = $this->cant;
            $return['precio'] = $this->price;
            $return['subtotal'] = $this->subtotal;
            $return['inStorage'] = $this->inStorage ? true : false;
            $return['session'] = $this->session;
            $return['descripcion'] = $this->descripcion;
            $return['created'] = $this->user->name ? $this->user->name : "";
            $return['created_at'] = $this->created_at->diffForHumans();
            $return['updated_at'] = $this->updated_at->diffForHumans();
        }

        return $return;
    }
}
