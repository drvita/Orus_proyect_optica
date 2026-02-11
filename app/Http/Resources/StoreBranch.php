<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StoreBranch extends JsonResource
{

    public function toArray($request)
    {
        $return = [];

        if ($this->id) {
            $return['id'] = $this->id;
            $return['branch_id'] = $this->branch_id;
            $return['store_item_id'] = $this->store_item_id;
            $return['cant'] = $this->cant;
            $return['price'] = $this->price;
            $return['lots'] = $this->relationLoaded('lots') ? StoreLotStore::collection($this->lots) : null;
        }

        return $return;
    }
}
