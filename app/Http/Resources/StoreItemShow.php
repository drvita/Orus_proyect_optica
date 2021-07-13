<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreItemShow extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $return['id'] = $this->id;
        $return['producto'] = $this->name;
        $return['codigo'] = $this->code ? $this->code : "";
        $return['c_barra'] = $this->codebar ? $this->codebar : "";
        $return['graduacion'] = $this->grad ? $this->grad : "";
        $return['marca'] = new BrandShort($this->brand);
        $return['unidad'] = $this->unit;
        $return['cantidades'] = $this->cant;
        $return['precio'] = $this->price;
        $return['categoria'] = new CategoryStore($this->categoria);
        $return['lotes'] = $this->lote ? count($this->lote) : 0;
        $return['proveedor'] = new Contact($this->supplier);
        return $return;
    }
}
