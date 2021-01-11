<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreItem extends JsonResource{

    public function toArray($request){
        $return['id'] = $this->id;
        $return['producto'] = $this->name;
        $return['codigo'] = $this->code ? $this->code : "";
        $return['c_barra'] = $this->codebar ? $this->codebar : "";
        $return['graduacion'] = $this->grad ? $this->grad : "+000000";
        $return['marca'] = new BrandShort($this->brand);
        $return['unidad'] = $this->unit;
        $return['cantidades'] = $this->cant;
        $return['precio'] = $this->price;
        $return['categoria'] = new CategoryStore($this->categoria);
        $return['lotes'] = $this->lote ? count($this->lote) : 0;
        $return['proveedor'] = new Contact($this->supplier);
        $return['created'] = $this->user->name;
        $return['created_at'] = $this->created_at->format('Y-m-d H:i');
        $return['updated_at'] = $this->updated_at->format('Y-m-d H:i');
        return $return;
    }
}
