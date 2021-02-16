<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Payment extends JsonResource{

    public function toArray($request){
        $return['id'] = $this->id;
        $return['metodo'] = $this->metodopago;
        $return['banco'] = $this->bank_id ? $this->bankName->value : "";
        $return['banco_id'] = $this->bank_id;
        $return['Autorizacion'] = $this->auth;
        $return['total'] = $this->total;
        $return['sale'] = $this->sale_id;
        $return['created_user'] = $this->user->name;
        $return['created_at'] = $this->created_at->format('Y-m-d');
        $return['updated_at'] = $this->updated_at->format('Y-m-d');
            
        return $return;
    }
}
