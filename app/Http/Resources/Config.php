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
            if (is_string($this->value)) {
                $value = json_decode($this->value, true);
            } else {
                $value = $this->value;
            }

            $return['id'] = $this->id;
            $return['name'] = $this->name;
            $return['data'] = $value;
        }

        return $return;
    }
}
