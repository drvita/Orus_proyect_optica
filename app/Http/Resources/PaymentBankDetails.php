<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentBankDetails extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request){
        //$return['banco'] = $this->bank_id ? $this->bankName->value : "";
        $return['bank_id'] = $this->bank_id;
        $return['total'] = $this->total;
            
        return $return;
    }
}
