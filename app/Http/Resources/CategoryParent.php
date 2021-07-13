<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryParent extends JsonResource
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
            $return['name'] = $this->name;
            $return['meta'] = getShortNameCat($this->name);
            $return['parent'] = new CategoryParent($this->parent);

            //old
            $return['categoria'] = $this->name;
            $return['depende_de'] = new CategoryParent($this->parent);
        }
            
        return $return;
    }
}