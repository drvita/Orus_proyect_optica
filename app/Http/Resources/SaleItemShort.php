<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleItemShort extends JsonResource
{

    public function toArray($request)
    {
        $return = [];
        $version = $request->input("version", 1);

        if (isset($this->id)) {
            $lot = $this->whenLoaded('lot', function () {
                return $this->lot;
            });

            $return['id'] = $this->id;
            $return['store_items_id'] = $this->whenLoaded('item', function () {
                return $this->item->id;
            });

            if ($version == 2) {
                $category = $this->item->relationLoaded('categoria')
                    ? $this->item->categoria->root
                    : null;
                $return['in_branch'] = $this->when($this->relationLoaded('item') && $this->item->relationLoaded('inBranch'), function () {
                    return $this->item->inBranch->filter(function ($item) {
                        return $item->cant > 0;
                    })->map(function ($item) {
                        return [
                            'branch_id' => $item->branch_id,
                            'cant' => $item->cant,
                            'price' => $item->price,
                            'lots' => $item->relationLoaded('lots') ? StoreLotStore::collection($item->lots) : null,
                        ];
                    });
                });
                $return['code'] = $this->whenLoaded('item', function () {
                    return $this->item->code;
                });
                $return['codebar'] = $this->whenLoaded('item', function () {
                    return $this->item->codebar;
                });
                $return['out'] = $this->out;
                $return['inStorage'] = $this->inStorage;
                $return['category'] = $category;
            }

            $return['name'] = $this->item?->name;
            $return['cant'] = $this->cant;
            $return['price'] = $this->price;
            $return['subtotal'] = $this->cant * $this->price;
            $return['description'] = $this->descripcion;
            $return['lot'] = $lot?->num_invoice ?? "";
        }

        return $return;
    }
}
