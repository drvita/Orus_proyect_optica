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

        if (isset($this->id)) {
            $value = json_decode($this->value);
            $return['id'] = $this->id;
            $return['name'] = $this->name;
            $return['data'] = $value ? $value : $this->value;
            // $return['values'] = $value ? $value : $this->value;
        }

        return $return;
    }
}
