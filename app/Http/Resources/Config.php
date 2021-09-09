<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Config extends JsonResource
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

        if(isset($this->id)){
            $value = json_decode($this->value);

            //dd($value);

            $return['id'] = $this->id;
            $return['name'] = $value ? $value : $this->value;
        }
        
        return $return;
    }
}
