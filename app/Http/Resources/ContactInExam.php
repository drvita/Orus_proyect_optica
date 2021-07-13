<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ExamToExams as ExamsResource;
use Carbon\Carbon;

class ContactInExam extends JsonResource {

    public function toArray($request){
        $edad = $this->birthday !== null ? $this->birthday->diffInYears( carbon::now() ) : 0;

        $return['id'] = $this->id;
        $return['nombre'] = $this->name;
        $return['rfc'] = $this->rfc;
        $return['email'] = $this->email;
        $return['telefonos'] = is_string($this->telnumbers) ? json_decode($this->telnumbers) : null;
        $return['f_nacimiento'] = $this->birthday
            ? $this->birthday->format('Y-m-d')
            : null;
        $return['edad'] = 1 < $edad && $edad < 120 ? $edad : 0;
        $return['examenes'] = ExamsResource::collection($this->exams);
        return $return;
    }
}
