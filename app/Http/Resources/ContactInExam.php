<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ExamToExams as ExamsResource;

class ContactInExam extends JsonResource {

    public function toArray($request){
        $return['id'] = $this->id;
        $return['nombre'] = $this->name;
        $return['rfc'] = $this->rfc;
        $return['email'] = $this->email;
        $return['telefonos'] = is_string($this->telnumbers) ? json_decode($this->telnumbers) : null;
        $return['f_nacimiento'] = $this->birthday
            ? $this->birthday->format('Y-m-d')
            : null;
        $return['examenes'] = ExamsResource::collection($this->exams);
        return $return;
    }
}
