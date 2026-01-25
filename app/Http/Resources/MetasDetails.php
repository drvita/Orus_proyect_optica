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

            $return["type"] = $this->key;

            if (in_array($this->key, ["updated", "deleted", "created"])) {
                if (!$user) {
                    $user = new \stdClass;
                }
                $value = $this->value;
                if (is_string($value)) {
                    $value = json_decode($value, true);
                }
                if (isset($value['inputs']['api_token'])) {
                    unset($value['inputs']['api_token']);
                }

                $return["data"] = [
                    "datetime" => $value['datetime'] ?? "",
                    "user" => [
                        "id" => $user->id ?? "",
                        "name" => $user->name ?? "",
                    ],
                    "inputs" => $value['inputs'] ?? new \stdClass,
                ];
            } else {
                $return["data"] = $this->value;
            }
        }

        return $return;
    }
}
