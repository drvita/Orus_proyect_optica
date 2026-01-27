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
            $return['id'] = $this->id;
            $return['name'] = $this->value['name'];
            $return['address'] = $this->value['address'];
            $return['phone'] = $this->value['phone'];
        }

        return $return;
    }
}
