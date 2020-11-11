<?php

namespace App\Http\Resources;
use App\Http\Resources\ContactShort;
use App\Http\Resources\ExamShort;
use App\Http\Resources\SaleItemShort;
use Illuminate\Http\Resources\Json\JsonResource;

class Order extends JsonResource{

    public function toArray($request){
        $return['id'] = $this->id;
        $return['paciente'] = new ContactShort($this->paciente);
        $return['examen'] = new ExamShort($this->examen);
        $return['session'] = $this->session;
        $return['productos'] = is_object($this->items) 
            ? SaleItemShort::collection($this->items) 
            : [];
        $return['caja'] = $this->ncaja;
        $return['folio_lab'] = $this->npedidolab;
        $return['laboratorio'] = new ContactShort($this->laboratorio);
        $return['observaciones'] = $this->observaciones;
        $return['estado'] = $this->status;
        $return['nota'] = $this->nota;
        $return['created'] = $this->user->name;
        $return['created_at'] = $this->created_at->format('Y-m-d H:i');
        $return['updated_at'] = $this->updated_at->format('Y-m-d H:i');
        return $return;
    }
}
