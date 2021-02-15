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
        $return['id'] = $this->id;
        $return['name'] = $this->value;
        return $return;
    }
}
