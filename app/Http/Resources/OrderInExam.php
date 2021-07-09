<?php

namespace App\Http\Resources;
use App\Http\Resources\ContactShort;
use App\Http\Resources\SaleItemShort as SaleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderInExam extends JsonResource{

    public function toArray($request){
        $return = [];

        if(isset($this->id)){
            $return['id'] = $this->id;
            $return['session'] = $this->session;
            $return['productos'] = $this->items
                ? SaleResource::collection($this->items) 
                : [];
            $return['caja'] = $this->ncaja;
            $return['folio_lab'] = $this->npedidolab;
            $return['laboratorio'] = $this->laboratorio 
                ? new ContactShort($this->laboratorio) 
                : null;
            $return['observaciones'] = $this->observaciones;
            $return['estado'] = $this->status;
            $return['nota'] = $this->nota;
            $return['created'] = $this->user;
        }
        
        return $return;
    }
}
