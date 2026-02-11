<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class StoreItem extends JsonResource
{

    public function toArray($request)
    {
        $return = [];
        $user = $request->user();

        if (isset($this->id)) {
            $cantAll = 0;
            $cantBranch = 0;
            $price = 0;
            $from = 0;

            if ($this->resource->relationLoaded('inBranch')) {
                $preferredId = $this->branch_default ?: $user->branch_id;
                $preferredRecord = null;
                $fallbackRecord = null;

                foreach ($this->inBranch as $item) {
                    $cantAll += $item->cant;

                    if ($item->branch_id === $preferredId) {
                        $preferredRecord = $item;
                    }

                    if ($item->cant > 0 && !$fallbackRecord) {
                        $fallbackRecord = $item;
                    }
                }

                $active = ($preferredRecord && $preferredRecord->cant > 0)
                    ? $preferredRecord
                    : ($fallbackRecord ?: $preferredRecord);

                if ($active) {
                    $price = $active->price;
                    $cantBranch = $active->cant;
                    $from = $active->branch_id;
                }
            }

            $return['id'] = $this->id;
            $return['name'] = $this->name;
            $return['code'] = $this->code ? $this->code : "";
            $return['barcode'] = $this->codebar ? $this->codebar : "";
            $return['grad'] = $this->grad ? $this->grad : "+000000";
            $return['und'] = $this->unit;
            $return['cant_total'] = $cantAll;
            $return['cant'] = $cantBranch;
            $return['price'] = $price;
            $return['from'] = $from;
            $return['branch_default'] = $this->branch_default;
            $return['brand'] = new BrandShort($this->brand);
            $return['branches'] = StoreBranch::collection($this->whenLoaded('inBranch'));
            $return['category'] = new CategorySimple($this->categoria);
            $return['supplier'] = new ContactStore($this->supplier);
            $return['created_at'] = $this->created_at ? $this->created_at->format('Y-m-d H:i') : null;
            $return['updated_at'] = $this->updated_at ? $this->updated_at->format('Y-m-d H:i') : null;
        }

        return $return;
    }
}
