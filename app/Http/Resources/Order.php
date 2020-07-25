<?php

namespace App\Http\Resources;
use App\Http\Resources\ContactShort;
use App\Http\Resources\ExamShort;
use Illuminate\Http\Resources\Json\JsonResource;

class Order extends JsonResource{

    public function toArray($request){
        $return['id'] = $this->id;
        $return['paciente'] = new ContactShort($this->paciente);
        $return['examen'] = new ExamShort($this->examen);
        $return['productos'] = $this->items;
        $return['mensajes'] = $this->mensajes;
        $return['caja'] = $this->ncaja;
        $return['folio_lab'] = $this->npedidolab;
        $return['laboratorio'] = $this->laboratorio;
        $return['observaciones'] = $this->observaciones;
        $return['armazon_code'] = $this->armazon_code;
        $return['armazon_name'] = $this->armazon_name;
        $return['estado'] = $this->status;
        $return['created'] = $this->user->name;
        $return['created_at'] = $this->created_at->diffForHumans();
        $return['updated_at'] = $this->updated_at->diffForHumans();
        return $return;
    }
}
