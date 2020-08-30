<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreLot extends JsonResource{

    public function toArray($request){
        $return['id'] = $this->id;
        $return['xml'] = $this->base64;
        $return['factura'] = $this->bill;
        $return['costo'] = $this->cost;
        $return['precio'] = $this->price;
        $return['cantidades'] = $this->amount;
        $return['producto'] = ($this->producto)? $this->producto->name : '';
        $return['created'] = $this->user->name;
        $return['created_at'] = $this->created_at->diffForHumans();
        $return['updated_at'] = $this->updated_at->diffForHumans();
        return $return;
    }
}
