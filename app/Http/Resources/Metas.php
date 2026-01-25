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
            $value = $this->value;
            if (is_string($value)) {
                $value = json_decode($value, true);
            }
            if (is_array($value)) {
                foreach ($value as $key => $value) {
                    $return[$key] = $value;
                }
            } else {
                $return[$this->key] = $value;
            }
        }

        return $return;
    }
}
