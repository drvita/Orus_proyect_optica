<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryShort extends JsonResource{

    public function toArray($request){
        $return['id'] = $this->id;
        $return['categoria'] = $this->name;
        $return['depende_de'] = new CategoryLast($this->parent);
        $return['hijos'] = CategoryShort::collection($this->categories);
            
        return $return;
    }
}
