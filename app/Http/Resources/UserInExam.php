<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserInExam extends JsonResource
{
    /**
     * Formatea la salida dando el formato de una api rest.
     * @return Json api rest
     */
    public function toArray($request)
    {
        $return = [];

        if (isset($this->id)) {
            $role = $this->getRoleNames();
            $role = $role ? $role : [];

            $return['id'] = $this->id;
            $return['username'] = $this->username;
            $return['name'] = $this->name;
            $return['email'] = $this->email;
            $return['role'] = count($role) ? $role[0] : "";
        }

        return $return;
    }
}
