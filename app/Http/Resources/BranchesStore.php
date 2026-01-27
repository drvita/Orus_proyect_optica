<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BranchesStore extends JsonResource
{

    public function toArray($request)
    {
        $return = [];

        if (isset($this->id)) {
            if (is_string($this->value)) {
                $data = json_decode($this->value, true);
            } else {
                $data = $this->value;
            }

            $return['id'] = $this->id;
            $return['name'] = $data['name'];
            $return['data']['name'] = $data['name'];
            $return['data']['address'] = $data['address'];
            $return['data']['phone'] = $data['phone'];
        }

        return $return;
    }
}
