<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

//use App\Http\Resources\ContactShort as ContactResource;
use App\Http\Resources\UserInExam as UserResource;

class ExamShort extends JsonResource
{

    public function toArray($request)
    {
        $return = [];

        if (isset($this->id)) {
            $return['id'] = $this->id;
            $return['status'] = $this->status;
            $return['created_at'] = $this->created_at->format('Y-m-d H:i');
        }
        return $return;
    }
}
