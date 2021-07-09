<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryInExam extends JsonResource{

    public function toArray($request){
        
        $return = [];

        if($this->id){
            $return['id'] = $this->id;
            $return['name'] = $this->name;
            $return['meta'] = $this->getCode();
            $return['parent'] = $this->parent ? new CategoryInExam($this->parent) : null;
        }
            
        return $return;
    }
}
