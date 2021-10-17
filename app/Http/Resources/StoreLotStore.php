<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreLotStore extends JsonResource
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

        if(isset($this->id)){
            $return['id'] = $this->id;
            $return['factura'] = $this->bill;
            $return['costo'] = $this->cost;
            $return['precio'] = $this->price;
            $return['cantidades'] = $this->amount;
        }
        

        return $return;
    }
}