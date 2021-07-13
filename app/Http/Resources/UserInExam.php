<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class UserInExam extends JsonResource
{
    /**
     * Formatea la salida dando el formato de una api rest.
     * @return Json api rest
     */
    public function toArray($request){
        $return = [];

        if(isset($this->id)){
            $return['id'] = $this->id;
            $return['username'] = $this->username;
            $return['name'] = $this->name;
            $return['email'] = $this->email;
            $return['rol'] = $this->rol;

        }
        return $return;
    }
}
