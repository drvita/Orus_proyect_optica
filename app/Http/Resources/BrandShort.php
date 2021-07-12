<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BrandShort extends JsonResource{

    public function toArray($request){
        $return['id'] = $this->id;
        $return['marca'] = $this->name;
        $return['name'] = $this->name;
        $return['created_at'] = $this->created_at->format('Y-m-d H:i');
        return $return;
    }
}
