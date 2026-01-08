<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExamToExams extends JsonResource
{

    public function toArray($request)
    {
        $return['id'] = $this->id;
        $return['estado'] = $this->status;
        $return['created'] = $this->user->name;
        $return['created_at'] = $this->created_at ? $this->created_at->format('Y-m-d H:i') : null;
        return $return;
    }
}
