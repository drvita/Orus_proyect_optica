<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryShort extends JsonResource{

    public function toArray($request){
        $return['id'] = $this->id;
        $return['nombre'] = $this->name;
            
        return $return;
    }
}
