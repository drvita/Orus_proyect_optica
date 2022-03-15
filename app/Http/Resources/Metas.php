<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Metas extends JsonResource
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

        if (isset($this->id)) {
            foreach ($this->value as $key => $value) {
                $return[$key] = $value;
            }
        }

        return $return;
    }
}