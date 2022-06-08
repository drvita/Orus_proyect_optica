<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategorySimple extends JsonResource
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
            $codeCategories = $this->getParentCategories($this);

            $return['id'] = $this->id;
            $return['name'] = $this->name;
            $return['code'] = $codeCategories;
        }

        return $return;
    }
}
