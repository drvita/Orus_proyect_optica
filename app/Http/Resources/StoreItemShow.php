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
            $return['graduacion'] = $this->grad ? $this->grad : "";
            $return['marca'] = new BrandShort($this->brand);
            $return['unidad'] = $this->unit;
            $return['cant_total'] = $cant;
            $return['categoria'] = new CategoryStore($this->categoria);
            $return['lotes'] = StoreLotStore::collection($this->lote);
            $return['proveedor'] = new ContactStore($this->supplier);
            $return['inBranches'] = StoreBranch::collection($this->inBranch);
            $return['branch_default'] = $this->branch_default;
        }
        
        return $return;
    }
}