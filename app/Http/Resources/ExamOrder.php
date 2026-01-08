<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExamOrder extends JsonResource
{

    public function toArray($request)
    {
        $return = [];

        if (isset($this->id)) {
            $return['id'] = $this->id;
            $return['status'] = $this->status;
            $return['created_at'] = $this->created_at ? $this->created_at->format('Y-m-d H:i') : null;
        }
        return $return;
    }
}
