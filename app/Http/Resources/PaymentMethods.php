<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentMethods extends JsonResource
{

    public function toArray($request)
    {
        $return = [];

        if (isset($this->total)) {
            $return['method'] = $this->methodName($this->metodopago);
            $return['total'] = $this->total;
        }

        return $return;
    }

    function methodName($id)
    {
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