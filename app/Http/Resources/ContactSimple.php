<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ContactSimple extends JsonResource
{

    public function toArray($request)
    {

        $return = [];

        if (isset($this->id)) {
            $return['id'] = $this->id;
            $return['name'] = $this->name;
            $return['email'] = $this->email;
            $return['type'] = $this->type;
            $return['age'] = $this->age;
            $return['birthday'] = $this->age > 0 ? $this->birthday->format('Y-m-d') : null;
            // $return['phones'] =  $this->telnumbers;
            $return['phones'] = new PhoneNumberCollection($this->whenLoaded('phones'));
            $return['created_at'] = $this->created_at ? $this->created_at->format('Y-m-d H:i') : null;
        }

        return $return;
    }
}
