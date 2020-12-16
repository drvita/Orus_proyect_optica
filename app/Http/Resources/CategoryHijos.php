<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryHijos extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $return['id'] = $this->id;
        $return['categoria'] = $this->name;
        $return['hijos'] = CategoryHijos::collection($this->categories);
            
        return $return;
    }
}
