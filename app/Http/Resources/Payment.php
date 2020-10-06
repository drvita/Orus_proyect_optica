<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Payment extends JsonResource{

    public function toArray($request){
        $return['id'] = $this->id;
        $return['metodo'] = $this->metodopago;
        $return['banco'] = $this->banco;
        $return['Autorizacion'] = $this->auth;
        $return['total'] = $this->total;
        $return['created_user'] = $this->user->name;
        $return['created_at'] = $this->created_at->format('Y-m-d');
        $return['updated_at'] = $this->updated_at->format('Y-m-d');
            
        return $return;
    }
}
