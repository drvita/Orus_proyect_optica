<?php

namespace App\Http\Resources;
use App\Http\Resources\ContactShort;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamShort extends JsonResource{

    public function toArray($request){
        $return['id'] = $this->id;
        $return['esferaoi'] = $this->esferaoi;
        $return['esferaod'] = $this->esferaod;
        $return['cilindroi'] = $this->cilindroi;
        $return['cilindrod'] = $this->cilindrod;
        $return['ejeoi'] = $this->ejeoi;
        $return['ejeod'] = $this->ejeod;
        $return['adicioni'] = $this->adicioni;
        $return['adiciond'] = $this->adiciond;
        $return['dpoi'] = $this->dpoi;
        $return['dpod'] = $this->dpod;
        $return['alturaoi'] = $this->alturaoi;
        $return['alturaod'] = $this->alturaod;
        $return['estado'] = ($this->status)?"Terminado":"En proceso";
        return $return;
    }
}
