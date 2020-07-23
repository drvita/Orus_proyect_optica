<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Category as CategoryResource;

class Category extends JsonResource{

    public function toArray($request){
        $return['id'] = $this->id;
        $return['categoria'] = $this->name;
        $return['descripcion'] = $this->descripcion;
        $return['depende_de'] = ($this->category_id > 0)? $this->parent->name: null;
        $return['hijos'] = CategoryResource::collection($this->categories);
        $return['created_user'] = $this->user->name;
        $return['created_at'] = $this->created_at->diffForHumans();
        $return['updated_at'] = $this->updated_at->diffForHumans();
            
        return $return;
    }
}
