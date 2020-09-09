<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class Contact extends JsonResource {

    public function toArray($request){
        $return['id'] = $this->id;
        $return['nombre'] = $this->name;
        $return['rfc'] = $this->rfc? $this->rfc : '';
        $return['email'] = $this->email;
        $return['tipo'] = $this->type;
        $return['empresa'] = $this->business;
        $return['telefonos'] = $this->telnumbers;
        $return['f_nacimiento'] = ($this->birthday)?$this->birthday->format('Y-m-d'):null;
        $return['domicilio'] = $this->domicilio;
        $return['created_user'] = $this->user->name;
        $return['created_at'] = $this->created_at->diffForHumans();
        $return['updated_at'] = $this->updated_at->diffForHumans();
        return $return;
    }
}
