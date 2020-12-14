<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryLast extends JsonResource{

    public function toArray($request){
        $parent = $this->parent;
        if($this->parent && !$this->parent->category_id){
            if(!$this->parent->category_id) $parent->hijos = $this->parent->categories;
        }
        

        $return['id'] = $this->id;
        $return['categoria'] = $this->name;
        $return['depende_de'] = $parent;
        $return['hijos'] = CategoryLast::collection($this->categories);
        
            
        return $return;
    }
}
