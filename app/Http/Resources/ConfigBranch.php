<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ConfigBranch extends JsonResource
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
                $value = json_decode($this->value);
            } else {
                $value = $this->value;
            }

            $return['id'] = $this->id;
            $return['name'] = $value['name'];
        }

        return $return;
    }
}
