<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactShort extends JsonResource {

    public function toArray($request){
        $return['id'] = $this->id;
        $return['nombre'] = $this->name;
        $return['rfc'] = $this->rfc;
        $return['email'] = $this->email;
        $return['telefonos'] = $this->telnumbers;
        $return['f_nacimiento'] = ($this->birthday)?$this->birthday->format('Y-m-d'):null;
        return $return;
    }
}
