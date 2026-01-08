<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Messenger extends JsonResource
{

    public function toArray($request)
    {

        $return['id'] = $this->id;
        $return['tabla'] = $this->table;
        $return['registro'] = $this->idRow;
        $return['mensaje'] = $this->message;
        $return['para'] = $this->user ? $this->user : null;
        $return['created_user'] = $this->creador;
        $return['created_at'] = $this->created_at ? $this->created_at->format('Y-m-d H:i') : null;
        $return['updated_at'] = $this->updated_at ? $this->updated_at->format('Y-m-d H:i') : null;
        return $return;
    }
}
