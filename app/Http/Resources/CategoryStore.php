<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryStore extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $return = [];
        if($this->id){
            $return['id'] = $this->id;
            $return['categoria'] = $this->name;
        }
            
        return $return;
    }
}
