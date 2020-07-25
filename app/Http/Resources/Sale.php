<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class Sale extends JsonResource{

    public function toArray($request){
        $return['id'] = $this->id;
        $return['session'] = $this->session;
        $return['productos'] = $this->items;
        $return['metodopago'] = $this->metodopago;
        $return['subtotal'] = $this->subtotal;
        $return['descuento'] = $this->descuento;
        $return['anticipo'] = $this->anticipo;
        $return['total'] = $this->total;
        $return['cliente'] = $this->cliente->name;
        $return['pedido'] = ($this->pedido)? $this->pedido: null;
        $return['created'] = $this->user->name;
        $return['created_at'] = $this->created_at->diffForHumans();
        $return['updated_at'] = $this->updated_at->diffForHumans();
        return $return;
    }
}
