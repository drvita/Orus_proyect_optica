<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User as UserResource;

class Category extends JsonResource{

    public function toArray($request){
        $return = [];

        
        if(isset($this->id)){
            $return['id'] = $this->id;
            $return['name'] = $this->name;
            $return['meta'] = $this->getCode();

            $return['parent'] = $this->parent ? new CategoryParent($this->parent) : null;
            $return['sons'] =  count($this->sons) ? CategorySons::collection($this->sons) : null;
            $return['created'] = new UserResource($this->user);

            //Old
            $return['categoria'] = $this->name;
            $return['depende_de'] = $this->parent ? new CategoryParent($this->parent) : null;
            $return['hijos'] = count($this->sons) ? CategorySons::collection($this->sons) : null;
            $return['created_user'] = $this->user->name;
        }
        
            
        return $return;
    }
}
