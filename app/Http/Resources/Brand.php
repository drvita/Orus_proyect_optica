<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Brand extends JsonResource{

    public function toArray($request){
        $return['id'] = $this->id;
        $return['nombre'] = $this->name;
        $return['proveedor'] = new Contact($this->proveedor);
        $return['created_user'] = $this->user->name;
        $return['created_at'] = $this->created_at->format('Y-m-d H:i');
        $return['updated_at'] = $this->updated_at->format('Y-m-d H:i');
        return $return;
    }
}
