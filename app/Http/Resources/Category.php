<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\CategoryWithSons as CategoryResource;

class Category extends JsonResource{

    public function toArray($request){
        $return = [];

        
        if(isset($this->id)){
            $return['id'] = $this->id;
            $return['name'] = $this->name;
            $return['meta'] = $this->getCode();

            $return['parent'] = $this->parent ? new Category($this->parent) : null;
            $return['sons'] =  count($this->sons) ? CategoryResource::collection($this->sons) : null; //$this->sons;
            $return['created'] = new UserResource($this->user);
        }
        
            
        return $return;
    }
}
