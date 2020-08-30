<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Atm extends JsonResource{

    public function toArray($request){
        $return['id'] = $this->id;
        $return['efectivo'] = $this->efectivo;
        $return['tarjetas'] = $this->tarjetas;
        $return['cheques'] = $this->cheques;
        $return['venta'] = $this->venta;
        $return['session_id'] = $this->session_id;
        $return['created_user'] = $this->user->name;
        $return['created_at'] = $this->created_at->diffForHumans();
        $return['updated_at'] = $this->updated_at->diffForHumans();
            
        return $return;
    }
}
