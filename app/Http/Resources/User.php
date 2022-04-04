<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    /**
     * Formatea la salida dando el formato de una api rest.
     * @return Json api rest
     */
    public function toArray($request)
    {
        $return = [];
        if (isset($this->id)) {
            $permissions = $this->getAllPermissions();
            $permissionsArray = [];

            if (count($permissions)) {
                foreach ($permissions as $val) {
                    $permissionsArray[] = $val['name'];
                }
            }

            $return =  [
                'id' => $this->id,
                'username' => $this->username,
                'name' => $this->name,
                'email' => $this->email,
                'session' => $this->session,
                'branch' => new Config($this->branch),
                'created_at' => $this->created_at->format('Y-m-d H:i'),
                'updated_at' => $this->updated_at->format('Y-m-d H:i'),
                'roles' => $this->getRoleNames(),
                'permissions' => $permissionsArray,
            ];
        }

        return $return;
    }
}
