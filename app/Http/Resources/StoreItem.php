<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreItem extends JsonResource{

    public function toArray($request){
        $return = [];
        

        if(isset($this->id)){
            $cant = 0;
            if($this->inBranch){
                foreach ($this->inBranch as $item) {
                    $cant += $item->cant;
                }
            }

            $return['id'] = $this->id;
            $return['producto'] = $this->name;
            $return['codigo'] = $this->code ? $this->code : "";
            $return['c_barra'] = $this->codebar ? $this->codebar : "";
            $return['graduacion'] = $this->grad ? $this->grad : "+000000";
            $return['marca'] = new BrandShort($this->brand);
            $return['unidad'] = $this->unit;
            $return['cant_total'] = $cant;
            //$return['cantidades'] = $this->cant;
            $return['precio'] = $this->price;
            //$return['categoria'] = new CategoryParent($this->categoria);
            $return['lotes'] = $this->lote ? count($this->lote) : 0;
            $return['proveedor'] = new Contact($this->supplier);
            $return['created'] = new UserInExam($this->user);
            $return['created_at'] = $this->created_at->format('Y-m-d H:i');
            $return['updated_at'] = $this->updated_at->format('Y-m-d H:i');
        }

        return $return;
    }
}