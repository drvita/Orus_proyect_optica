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
        $return['telefonos'] =  is_string($this->telnumbers) ? json_decode($this->telnumbers) : $this->telnumbers;
        $return['f_nacimiento'] = $this->birthday && intval($this->birthday->format('Y')) > 1900 ? $this->birthday->format('Y-m-d') : null;
        $return['domicilio'] = is_string($this->domicilio) ? json_decode($this->domicilio) : $this->domicilio;
        $return['enUso'] = count($this->buys) + count($this->orders) + count($this->supplier) + count($this->exams) + count($this->brands);
        $return['marcas'] = BrandShort::collection($this->brands);
        $return['created_user'] = $this->user->name;
        $return['created_at'] = $this->created_at->format('Y-m-d H:i');
        $return['updated_at'] = $this->updated_at->format('Y-m-d H:i');
        return $return;
    }
}
