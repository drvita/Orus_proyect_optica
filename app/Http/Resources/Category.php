<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User as UserResource;

class Category extends JsonResource
{

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
            $return['parent'] = $this->parent ? new CategoryParent($this->parent) : null;
            $return['sons'] =  count($this->sons) ? CategorySons::collection($this->sons) : null;
        }


        return $return;
    }
}
