<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;

class MetasDetails extends JsonResource
{
    private static $userCache = [];

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
            $userId = $this->value['user_id'] ?? null;
            $user = null;

            if ($userId) {
                if (!isset(self::$userCache[$userId])) {
                    self::$userCache[$userId] = User::find($userId);
                }
                $user = self::$userCache[$userId];
            }

            if (!$user) {
                $user = new \stdClass;
            }

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
