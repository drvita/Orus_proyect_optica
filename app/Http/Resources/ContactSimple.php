<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use App\Http\Resources\ExamShort as ExamResource;
use App\Http\Resources\SaleInContact as SaleResource;
use App\Http\Resources\BrandShort as BrandResource;
use App\Http\Resources\OrderInExam as OrderResource;

class ContactSimple extends JsonResource {

    public function toArray($request){
        
        $return = [];

        if(isset($this->id)){
            $return['id'] = $this->id;
            $return['nombre'] = $this->name;
            $return['email'] = $this->email;
            $return['tipo'] = $this->type;
            $return['f_nacimiento'] = $this->birthday && intval($this->birthday->format('Y')) > 1900 ? $this->birthday->format('Y-m-d') : null;
            $return['created_at'] = $this->created_at->format('Y-m-d H:i');
            $return['updated_at'] = $this->updated_at->format('Y-m-d H:i');
        }

        return $return;
    }
}
