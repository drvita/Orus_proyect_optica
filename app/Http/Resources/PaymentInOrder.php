<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentInOrder extends JsonResource{

    public function toArray($request){
        $return = [];

        if(isset($this->id)){
            $return['id'] = $this->id;
            $return['metodopago'] = $this->metodopago;
            $return['metodoname'] = $this->methodName($this->metodopago);
            $return['banco'] = $this->bankName;
            $return['bank_id'] = $this->bankName ? $this->bankName->id : null;
            $return['auth'] = $this->auth;
            $return['total'] = $this->total;
            $return['details'] = $this->details;
            $return['created'] = new UserInExam($this->user);
            $return['created_at'] = $this->created_at->format('Y-m-d');
        }
            
        return $return;
    }

    function methodName ($id) {
        switch ($id) {
            case 1:
                return "efectivo";
            case 2:
                return "tarjeta debito";
            case 3:
                return "tarjeta de credito";
            case 4:
                return "la marina";
            case 5:
                return "cheque";
            case 6:
                return "transferencia";
            default:
                return "otro";
        }
    }
}
