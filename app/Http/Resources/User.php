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

            $activity = $this->metas()
                ->where("key", ["updated", "deleted", "created"])
                ->orderBy("id", "desc")
                ->take(25)
                ->get();
            $obj = [
                'id' => 0,
                'key' => 'created',
                'value' => json_encode([
                    "datetime" => $this->created_at,
                    "user_id" => 1
                ])
            ];
            $obj = json_decode(json_encode($obj), false);
            $obj->value = json_decode($obj->value, true);
            $activity->push($obj);

            $return =  [
                'id' => $this->id,
                'username' => $this->username,
                'name' => $this->name,
                'email' => $this->email,
                'session' => $this->session,
                'branch' => new Config($this->branch),
                'roles' => $this->getRoleNames(),
                'permissions' => $permissionsArray,
                'activity' => MetasDetails::collection($activity),
                'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i') : null,
                'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i') : null
            ];
        }

        return $return;
    }
}
