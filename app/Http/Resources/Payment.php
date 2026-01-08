<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Payment extends JsonResource
{

    public function toArray($request)
    {
        $return = [];

        if (isset($this->id)) {
            $return['id'] = $this->id;
            $return['metodopago'] = $this->metodopago;
            $return['metodoname'] = getPaymentName($this->metodopago);
            $return['banco'] = $this->bankName;
            $return['bank_id'] = $this->bankName ? $this->bankName->id : null;
            $return['auth'] = $this->auth;
            $return['total'] = $this->total;
            $return['details'] = $this->details;
            $return['sale'] = new SaleInPayment($this->SaleDetails);
            $return['created_user'] = $this->user->name;
            $return['created'] = new UserInExam($this->user);
            $return['branch'] = new Config($this->branch);
            $return['created_at'] = $this->created_at ? $this->created_at->format('Y-m-d') : null;
            $return['updated_at'] = $this->updated_at ? $this->updated_at->format('Y-m-d') : null;
        }

        return $return;
    }
}
