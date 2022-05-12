<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategorySons extends JsonResource
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
            $return['meta'] = $this->getCode();
            $return['code'] = $codeCategories['codeCategory'];
            $return['codeName'] = $codeCategories['codeNameCategory'];
            $return['sons'] = $this->sons ? CategorySons::collection($this->sons) : null;
        }

        return $return;
    }
}
