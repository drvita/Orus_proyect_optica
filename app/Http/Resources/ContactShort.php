<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ExamShort as ExamResource;

class ContactShort extends JsonResource
{

    public function toArray($request)
    {
        $return['id'] = $this->id;
        $return['nombre'] = strtolower($this->name);
        $return['rfc'] = $this->rfc;
        $return['email'] = strtolower($this->email);
        return $return;
    }
}
