<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderInExam extends JsonResource
{

    public function toArray($request)
    {
        $return = [];

        if (isset($this->id)) {
            $return['id'] = $this->id;
            $return['status'] = $this->status;
            $return['order_foreing'] = $this->npedidolab;
            $return['nota'] = $this->nota ? $this->nota->id : null;
            $return['branch'] = new ConfigBranch($this->branch);
            $return['created_at'] = $this->created_at->format('Y-m-d H:i');
        }

        return $return;
    }
}
