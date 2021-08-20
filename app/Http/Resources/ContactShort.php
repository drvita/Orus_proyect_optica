<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ExamShort as ExamResource;

class ContactShort extends JsonResource {

    public function toArray($request){
        $return['id'] = $this->id;
        $return['nombre'] = $this->name;
        $return['rfc'] = $this->rfc;
        $return['email'] = $this->email;
        $return['telefonos'] = is_string($this->telnumbers) ? json_decode($this->telnumbers) : $this->telnumbers;
        $return['f_nacimiento'] = ($this->birthday)?$this->birthday->format('Y-m-d'):null;
        $return['examenes'] = ExamResource::collection($this->exams);
        return $return;
    }
}
