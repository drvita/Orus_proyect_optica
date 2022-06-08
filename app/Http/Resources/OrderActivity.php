<?php

namespace App\Http\Resources;

use App\Http\Resources\ContactShort;
use App\Http\Resources\ExamShort;
use App\Http\Resources\SaleItemShort;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderActivity extends JsonResource
{

    public function toArray($request)
    {
        $return = [];

        if ($this->id) {
            $items = is_string($this->items) ? json_decode($this->items, true) : $this->items;
            $activity = $this->metas()->whereIn("key", ["updated", "deleted", "created", "created branch"])->get();

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
            $activity->prepend($obj);

            $return['id'] = $this->id;
            $return['session'] = $this->session;
            $return['ncaja'] = $this->ncaja;
            $return['npedidolab'] = $this->npedidolab;
            $return['observaciones'] = $this->observaciones;
            $return['status'] = $this->status;

            $return['paciente'] = new ContactShort($this->paciente);
            $return['exam'] = new ExamShort($this->examen);
            $return['items'] = SaleItemShort::collection($items);
            $return['laboratorio'] = new ContactShort($this->laboratorio);
            $return['activity'] = MetasDetails::collection($activity);
            $return['nota'] = new SaleShort($this->nota);
            $return['branch'] = new ConfigBranch($this->branch);

            $return['created_at'] = $this->created_at->format('Y-m-d H:i');
            $return['updated_at'] = $this->updated_at->format('Y-m-d H:i');
        }

        return $return;
    }
}
