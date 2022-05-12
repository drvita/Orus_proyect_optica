<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentSimple extends JsonResource
{

    public function toArray($request)
    {
        $return = [];

        if (isset($this->id)) {
            $return['id'] = $this->id;
            $return['metodopago'] = $this->metodopago;
            $return['banco'] = $this->bankName;
            $return['bank_id'] = $this->bankName ? $this->bankName->id : null;
            $return['auth'] = $this->auth;
            $return['total'] = $this->total;
            $return['details'] = $this->details;
            $return['branch'] = new ConfigBranch($this->branch);
            $return['created_at'] = $this->created_at;
        }

        return $return;
    }
}
