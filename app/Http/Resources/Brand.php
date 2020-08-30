<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Brand extends JsonResource{

    public function toArray($request){
        $return['id'] = $this->id;
        $return['marca'] = $this->name;
        $return['proveedor'] = ($this->proveedor)? $this->proveedor->name: null;
        $return['created_user'] = $this->user->name;
        $return['created_at'] = $this->created_at->diffForHumans();
        $return['updated_at'] = $this->updated_at->diffForHumans();
            
        return $return;
    }
}
