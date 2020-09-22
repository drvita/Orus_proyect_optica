<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CategoryShort as CategoryResource;

class StoreItemShort extends JsonResource{

    public function toArray($request){
        $return['id'] = $this->id;
        $return['producto'] = $this->name;
        $return['unidad'] = $this->unit;
        $return['cantidades'] = $this->cant;
        return $return;
    }
}
