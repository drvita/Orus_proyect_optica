<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Atm extends JsonResource
{

    public function toArray($request)
    {
        $return = [];

        if (isset($this->id)) {
            $return['id'] = $this->id;
            $return['efectivo'] = $this->efectivo;
            $return['tipo'] = $this->type;
            $return['session'] = $this->session;
            $return['motivo'] = $this->motivo;
            $return['created_user'] = $this->user->name;
            $return['branch'] = new Config($this->branch);
            $return['created_at'] = $this->created_at ? $this->created_at->format('Y-m-d H:i') : null;
            $return['updated_at'] = $this->updated_at ? $this->updated_at->format('Y-m-d H:i') : null;
        }

        return $return;
    }
}
