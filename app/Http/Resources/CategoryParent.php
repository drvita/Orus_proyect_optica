<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryParent extends JsonResource
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

        if ($this->id) {
            $codeCategories = $this->getParentCategories($this);

            $return['id'] = $this->id;
            $return['name'] = $this->name;
            $return['meta'] = getShortNameCat($this->name);
            $return['code'] = $codeCategories['codeCategory'];
            $return['codeName'] = $codeCategories['codeNameCategory'];
            $return['parent'] = new CategoryParent($this->parent);
        }

        return $return;
    }
}
