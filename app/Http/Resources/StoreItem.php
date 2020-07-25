<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreItem extends JsonResource{

    public function toArray($request){
        $return['id'] = $this->id;
        $return['producto'] = $this->name;
        $return['codigo'] = $this->code;
        $return['c_barra'] = $this->codebar;
        $return['unidad'] = $this->unit;
        $return['cantidades'] = $this->cant;
        $return['precio'] = $this->price;
        $return['categoria'] = $this->categoria->name;
        $return['created'] = $this->user->name;
        $return['created_at'] = $this->created_at->diffForHumans();
        $return['updated_at'] = $this->updated_at->diffForHumans();
        return $return;
    }
}
