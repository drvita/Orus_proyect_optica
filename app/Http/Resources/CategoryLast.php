<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryLast extends JsonResource{

    public function toArray($request){
        $return['id'] = $this->id;
        $return['categoria'] = $this->name;
            
        return $return;
    }
}
