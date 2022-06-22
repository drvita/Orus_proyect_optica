<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Sale extends JsonResource
{

    public function toArray($request)
    {
        $return = [];
        $activity = $this->metas()->whereIn("key", ["updated", "deleted", "created", "created item", "deleted item", "deleted payment", "created payment"])->orderBy("id", "desc")->get();

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

        // dd($activity);

        if (isset($this->id)) {
            $return['id'] = $this->id;
            $return['session'] = $this->session;
            $return['subtotal'] = $this->subtotal;
            $return['discount'] = $this->descuento ? $this->descuento : 0;
            $return['total'] = $this->total;

            $return['items'] = SaleItemShort::collection($this->items);
            $return['customer'] = new ContactStore($this->cliente);
            $return['order'] = $this->order_id;
            $return['payments'] = PaymentSimple::collection($this->payments);
            $return['branch'] = new ConfigBranch($this->branch);
            $return["activity"] = MetasDetails::collection($activity);
            $return['created'] = new UserSimple($this->user);
            $return['updated'] = new UserSimple($this->user_updated);

            $return['deleted_at'] = $this->deleted_at ? $this->deleted_at->format('Y-m-d H:i') : null;
            $return['created_at'] = $this->created_at->format('Y-m-d H:i');
            $return['updated_at'] = $this->updated_at->format('Y-m-d H:i');
        }

        return $return;
    }
}
