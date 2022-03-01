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
    public function toArray($request)
    {
        $return = [];

        if (isset($this->bank_id)) {
            $return['name'] = $this->bankName["value"];
            $return['total'] = $this->total;
        }


        return $return;
    }
}