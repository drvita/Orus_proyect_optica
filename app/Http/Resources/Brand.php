<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Brand extends JsonResource
{

    public function toArray($request)
    {
        $return['id'] = $this->id;
        $return['name'] = $this->name;
        $return['supplier'] = new ContactStore($this->proveedor);
        return $return;
    }
}
