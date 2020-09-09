<?php

namespace App\Http\Resources;
use App\Http\Resources\CategoryShort;
use Illuminate\Http\Resources\Json\JsonResource;

class Category extends JsonResource{

    public function toArray($request){
        $return['id'] = $this->id;
        $return['categoria'] = $this->name;
        $return['depende_de'] = new CategoryShort($this->parent);
        $return['hijos'] = CategoryShort::collection($this->categories);
        $return['created_user'] = $this->user->name;
        $return['created_at'] = $this->created_at->diffForHumans();
        $return['updated_at'] = $this->updated_at->diffForHumans();
            
        return $return;
    }
}
