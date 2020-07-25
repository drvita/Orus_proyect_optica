<?php

namespace App\Http\Resources;
use App\Http\Resources\ContactShort;
use Illuminate\Http\Resources\Json\JsonResource;

class Exam extends JsonResource{

    public function toArray($request){
        $return['id'] = $this->id;
        $return['paciente'] = new ContactShort($this->paciente);
        $return['observaciones'] = $this->observaciones;
        $return['estado'] = $this->status;
        $return['created'] = $this->user->name;
        $return['created_at'] = $this->created_at->diffForHumans();
        $return['updated_at'] = $this->updated_at->diffForHumans();
        return $return;
    }
}
