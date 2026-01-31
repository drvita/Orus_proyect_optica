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
            $permissionsArray = $this->getAllPermissions()->pluck('name');

            $activity = $this->whenLoaded('metas', function () {
                return $this->activity;
            }, collect());

            if ($activity->isNotEmpty()) {
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
            }

            $return =  [
                'id' => $this->id,
                'username' => $this->username,
                'name' => $this->name,
                'email' => $this->email,
                'session' => $this->whenLoaded('session'),
                'branch' => new BranchesStore($this->whenLoaded('branch')),
                'roles' => $this->getRoleNames(),
                'permissions' => $permissionsArray,
                'phones' => $this->whenLoaded('phones'),
                'requestPasswordReset' => $this->remember_token,
                'networks' => $this->whenLoaded('metas', function () {
                    return SocialNetworkResource::collection($this->networks);
                }, collect()),
                'activity' => MetasDetails::collection($activity),
                'created_at' => $this->created_at?->format('Y-m-d H:i'),
                'updated_at' => $this->updated_at?->format('Y-m-d H:i')
            ];
        }

        return $return;
    }
}
