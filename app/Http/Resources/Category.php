<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class Category extends JsonResource{

    public function toArray($request){
        $return['id'] = $this->id;
        $return['categoria'] = $this->name;
        $return['depende_de'] = new CategoryPadre($this->parent);
        $return['hijos'] = CategoryHijos::collection($this->categories);
        $return['created_user'] = $this->user->name;
        $return['created_at'] = $this->created_at->diffForHumans();
        $return['updated_at'] = $this->updated_at->diffForHumans();
            
        return $return;
    }
}
