<?php

namespace App\Http\Resources;

use App\Http\Resources\ContactShort;
use Illuminate\Http\Resources\Json\JsonResource;

class Order extends JsonResource
{

    public function toArray($request)
    {
        $return = [];
        $version = $request->input("version", 1);

        if ($this->id) {

            $return['id'] = $this->id;

            $return['exam'] = new ExamOrder($this->examen);
            $return['paciente'] = new ContactSimple($this->paciente);
            $return['session'] = $this->session;
            $return['ncaja'] = $this->ncaja;
            $return['npedidolab'] = $this->npedidolab;
            $return['lab'] = new ContactShort($this->laboratorio);
            $return['observaciones'] = $this->observaciones;
            $return['status'] = $this->status;
            $return['sale'] = new SaleShort($this->nota);
            $return['branch'] = new ConfigBranch($this->branch);
            if ($version == 2) {
                $return['lab_complete'] = $this->whenLoaded('items', function () {
                    return $this->is_lab_complete;
                });
            }

            $return['created_at'] = $this->created_at ? $this->created_at->format('Y-m-d H:i') : null;
            $return['updated_at'] = $this->updated_at ? $this->updated_at->format('Y-m-d H:i') : null;
        }

        return $return;
    }
}
