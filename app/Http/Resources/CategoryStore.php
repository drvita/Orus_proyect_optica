<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryStore extends JsonResource
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
            $codeCategories = $this->getCodes($this);

            $return['id'] = $this->id;
            $return['categoria'] = $this->name;
            $return['code'] = $codeCategories['codeCategory'];
            $return['codeName'] = $codeCategories['codeNameCategory'];
        }

        return $return;
    }
    public function getCodes($item)
    {
        $codeCategory = "";
        $codeNameCategory = "";

        if ($item->parent) {
            if ($item->parent->parent) {
                if ($item->parent->parent->parent) {
                    $codeCategory = $item->parent->parent->parent->id . "|" . $item->parent->parent->id . "|" . $item->parent->id . "|" . $item->id;
                    $codeNameCategory = $item->parent->parent->parent->name . "|" . $item->parent->parent->name . "|" . $item->parent->name . "|" . $item->name;
                } else {
                    $codeCategory = $item->parent->parent->id . "|" . $item->parent->id . "|" . $item->id;
                    $codeNameCategory = $item->parent->parent->name . "|" . $item->parent->name . "|" . $item->name;
                }
            } else {
                $codeCategory = $item->parent->id . "|" . $item->id;
                $codeNameCategory = $item->parent->name . "|" . $item->name;
            }
        } else {
            $codeCategory =  (string) $item->id;
            $codeNameCategory = $item->name;
        }

        return [
            "codeCategory" => $codeCategory,
            "codeNameCategory" => $codeNameCategory,
        ];
    }
}