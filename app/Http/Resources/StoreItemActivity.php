<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class StoreItemActivity extends JsonResource
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
            $branchSelect = null;
            $activity = $this->metas()->whereIn("key", ["updated", "deleted", "created", "created branch"])->orderBy("id", "desc")->get();

            $obj = [
                'id' => 0,
                'key' => 'created',
                'value' => json_encode([
                    "datetime" => $this->created_at,
                    "user_id" => $this->user->id
                ])
            ];
            $obj = json_decode(json_encode($obj), false);
            $obj->value = json_decode($obj->value, true);
            $activity->push($obj);


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
                    $branchSelect = $this->branch_default;
                } else {
                    foreach ($branches as $item) {
                        $cantAll += $item['cant'];
                        if ($user->branch_id === $item['branch_id']) {
                            $price = $item['price'];
                            $cantBranch = $item['cant'];
                            $from = $user->branch_id;
                        }
                    }
                    $branchSelect = $user->branch_id;
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
            $return['branch_select'] = $branchSelect;
            $return['price'] = $price;
            $return['from'] = $from;
            $return['branch_default'] = $this->branch_default;

            $return['brand'] = new BrandShort($this->brand);
            $return['branches'] = StoreBranch::collection($this->inBranch);
            $return['activity'] = MetasDetails::collection($activity);
            $return['category'] = new CategoryStore($this->categoria);
            $return['supplier'] = new ContactStore($this->supplier);

            $return['created_at'] = $this->created_at->format('Y-m-d H:i');
            $return['updated_at'] = $this->updated_at->format('Y-m-d H:i');
        }

        return $return;
    }
}
