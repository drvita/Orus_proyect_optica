<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserSimple extends JsonResource
{
    /**
     * Formatea la salida dando el formato de una api rest.
     * @return Json api rest
     */
    public function toArray($request)
    {
        $return = [];
        if (isset($this->id)) {

            $return =  [
                'id' => $this->id,
                'username' => $this->username,
                'name' => $this->name,
                'role' => $this->role?->name,
            ];
        }

        return $return;
    }
}
