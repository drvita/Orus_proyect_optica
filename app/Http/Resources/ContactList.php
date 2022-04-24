<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ContactList extends JsonResource
{

    public function toArray($request)
    {

        $return = [];

        if (isset($this->id)) {
            $edad = $this->birthday !== null ? $this->birthday->diffInYears(carbon::now()) : 0;

            $return['id'] = $this->id;
            $return['name'] = $this->name;
            $return['email'] = $this->email;
            $return['type'] = $this->type;
            $return['phones'] =  new ContactPhones($this->telnumbers);
            $return['age'] = 1 < $edad && $edad < 120 ? $edad : 0;
            $return['business'] = $this->business;

            $return['enUso'] = count($this->buys) +
                count($this->brands) +
                count($this->exams) +
                count($this->supplier) +
                count($this->orders);

            $return["metadata"] = $this->metas->count() ? new Metas($this->metas[0]) : [];
            $return['created'] = new UserInExam($this->user);
            $return['created_at'] = $this->created_at->format('Y-m-d H:i');
            $return['updated_at'] = $this->updated_at->format('Y-m-d H:i');
        }

        return $return;
    }
}
