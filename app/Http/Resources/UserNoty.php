<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserNoty extends JsonResource
{
    /**
     * Formatea la salida dando el formato de una api rest.
     * @return Json api rest
     */
    public function toArray($request)
    {
        $return = [];
        if (isset($this->id)) {
            $permissionsArray = $this->getAllPermissions()->pluck('name');
            $activity = $this->whenLoaded('metas', function () {
                return $this->activity;
            }, collect());

            $return =  [
                'id' => $this->id,
                'username' => $this->username,
                'name' => $this->name,
                'email' => $this->email,
                'session' => $this->whenLoaded('session'),
                'branch' => new BranchesStore($this->whenLoaded('branch')),
                'roles' => $this->getRoleNames(),
                'permissions' => $permissionsArray,
                'unreadNotifications' => $this->unreadNotifications,
                'activity' => MetasDetails::collection($activity),
                'isAdmin' => $this->hasRole('admin'),
                'isVentas' => $this->hasRole('ventas'),
                'isdoctor' => $this->hasRole('doctor'),
            ];
        }

        return $return;
    }
}
