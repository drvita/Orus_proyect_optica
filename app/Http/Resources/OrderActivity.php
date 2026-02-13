<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderActivity extends JsonResource
{

    public function toArray($request)
    {
        $return = [];
        $version = $request->input("version", 1);

        if ($this->id) {
            $activity = $this->activity;

            $return['id'] = $this->id;
            $return['session'] = $this->session;
            $return['bi_box'] = $this->ncaja;
            $return['lab_order'] = $this->npedidolab;
            $return['bi_details'] = $this->observaciones;
            $return['observaciones'] = $this->observaciones;
            $return['status'] = $this->status;

            $return['paciente'] = $this->whenLoaded('paciente', new ContactSimple($this->paciente));
            $return['exam'] = $this->whenLoaded('examen', new ExamShort($this->examen));
            $return['items'] = $this->whenLoaded('items', SaleItemShort::collection($this->items));
            $return['lab'] = $this->whenLoaded('laboratorio', new ContactShort($this->laboratorio));
            $return['sale'] = $this->whenLoaded('nota', new SaleShort($this->nota));
            $return['branch'] = $this->whenLoaded('branch', new ConfigBranch($this->branch));
            $return['activity'] = MetasDetails::collection($activity);

            if ($version == 2) {
                $return['lab_complete'] = $this->whenLoaded('items', function () {
                    return $this->is_lab_complete;
                });
                $return['time_process'] = $this->time_process;
            }

            $return['created_at'] = $this->created_at ? $this->created_at->format('Y-m-d H:i') : null;
            $return['updated_at'] = $this->updated_at ? $this->updated_at->format('Y-m-d H:i') : null;
            $return['created_by'] = $this->whenLoaded('user', new UserSimple($this->user));
            $return['updated_by'] = $this->whenLoaded('user_updated', new UserSimple($this->user_updated));
        }

        return $return;
    }
}
