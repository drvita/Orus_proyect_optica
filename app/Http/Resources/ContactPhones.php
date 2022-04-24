<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContactPhones extends JsonResource
{

    public function toArray($request)
    {

        $return = [];
        $notice = isset($this["notices"]) ? $this["notices"] : "";
        $cell = isset($this["cell"]) ? $this["cell"] : "";
        $office = isset($this["office"]) ? $this["office"] : "";

        $return['notices'] = isset($this["t_casa"]) ? $this["t_casa"] : $notice;
        $return['cell'] = isset($this["t_movil"]) ? $this["t_movil"] : $cell;
        $return['office'] = isset($this["t_oficina"]) ? $this["t_oficina"] : $office;

        return $return;
    }
}
