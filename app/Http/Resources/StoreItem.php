<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class StoreItem extends JsonResource
{

    public function toArray($request)
    {
        $return = [];
        $user = Auth::user();

        if (isset($this->id)) {
            $cantAll = 0;
            $cantBranch = 0;
            $price = 0;
            $from = 0;


            if ($this->inBranch) {
                $branches = $this->inBranch->toArray();

                if ($this->branch_default) {
                    foreach ($branches as $item) {
                        $cantAll += $item['cant'];
                        if ($this->branch_default === $item['branch_id']) {
                            $price = $item['price'];
                            $cantBranch = $item['cant'];
                            $from = $this->branch_default;
                        }
                    }
                } else {
                    foreach ($branches as $item) {
                        $cantAll += $item['cant'];
                        if ($user->branch_id === $item['branch_id']) {
                            $price = $item['price'];
                            $cantBranch = $item['cant'];
                            $from = $user->branch_id;
                        }
                    }
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
            $return['branches'] = StoreBranch::collection($this->inBranch);
            $return['category'] = new CategorySimple($this->categoria);
            $return['supplier'] = new ContactStore($this->supplier);
            $return['created_at'] = $this->created_at ? $this->created_at->format('Y-m-d H:i') : null;
            $return['updated_at'] = $this->updated_at ? $this->updated_at->format('Y-m-d H:i') : null;
        }

        return $return;
    }
}
