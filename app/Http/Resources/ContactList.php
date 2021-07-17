<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use App\Http\Resources\ExamShort as ExamResource;
use App\Http\Resources\SaleInContact as SaleResource;
use App\Http\Resources\BrandShort as BrandResource;
use App\Http\Resources\OrderInExam as OrderResource;

class ContactList extends JsonResource {

    public function toArray($request){
        
        $return = [];
        $perPage = 10;

        if(isset($this->id)){
            $edad = $this->birthday !== null ? $this->birthday->diffInYears( carbon::now() ) : 0;

            $return['id'] = $this->id;
            $return['nombre'] = $this->name;
            $return['email'] = $this->email;
            $return['tipo'] = $this->type;
            $return['telefonos'] =  is_string($this->telnumbers) ? json_decode($this->telnumbers) : $this->telnumbers;
            $return['f_nacimiento'] = $this->birthday && intval($this->birthday->format('Y')) > 1900 ? $this->birthday->format('Y-m-d') : null;
            $return['edad'] = 1 < $edad && $edad < 120 ? $edad : 0;
            $return['lab'] = $this->business;

            $return['enUso'] = count($this->buys) + 
                                count($this->brands) + 
                                count($this->exams) + 
                                count($this->supplier) + 
                                count($this->orders);

            $return['created'] = new UserInExam($this->user);
            $return['created_at'] = $this->created_at->format('Y-m-d H:i');
            $return['updated_at'] = $this->updated_at->format('Y-m-d H:i');
        }

        return $return;
    }
}
