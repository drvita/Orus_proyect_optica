<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleItemShort extends JsonResource
{

    public function toArray($request)
    {
        $return = [];

        if (isset($this->id)) {
            $return['id'] = $this->id;
            $return['store_items_id'] = $this->item->id;
            $return['name'] = $this->item->name;
            $return['cant'] = $this->cant;
            $return['price'] = $this->price;
            $return['subtotal'] = $this->cant * $this->price;
            $return['description'] = $this->descripcion;
        }
        return $return;
    }
}
