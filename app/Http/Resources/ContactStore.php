<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ExamShort as ExamResource;

class ContactStore extends JsonResource
{

    public function toArray($request)
    {
        $return = [];

        if (isset($this->id)) {
            $return['id'] = $this->id;
            $return['name'] = $this->name;
            $return['phones'] =  new ContactPhones($this->telnumbers);
        }


        return $return;
    }
}
