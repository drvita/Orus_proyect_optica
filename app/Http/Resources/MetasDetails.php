<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\User;

class MetasDetails extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $return = [];

        if (isset($this->id)) {
            $user = $this->value['user_id'] ? User::find($this->value['user_id']) : new \stdClass;

            $return["type"] = $this->key;
            $return["data"] = [
                "datetime" => $this->value['datetime'] ?? "",
                "user" => [
                    "id" => $user->id ?? "",
                    "name" => $user->name ?? "",
                ],
                "inputs" => $this->value['inputs'] ?? new \stdClass,
            ];
        }

        return $return;
    }
}
