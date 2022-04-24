<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContactAddress extends JsonResource
{

    public function toArray($request)
    {

        $return = [];
        $street = isset($this["street"]) ? $this["street"] : "";
        $neighbornhood = isset($this["neighborhood"]) ? $this["neighborhood"] : "";
        $location = isset($this["location"]) ? $this["location"] : "";
        $state = isset($this["state"]) ? $this["state"] : "";
        $zip = isset($this["zip"]) ? $this["zip"] : "";


        $return['street'] = isset($this["calle"]) ? $this["calle"] : $street;
        $return['neighborhood'] = isset($this["colonia"]) ? $this["colonia"] : $neighbornhood;
        $return['location'] = isset($this["municipio"]) ? $this["municipio"] : $location;
        $return['state'] = isset($this["estado"]) ? $this["estado"] : $state;
        $return['zip'] = isset($this["cp"]) ? $this["cp"] : $zip;

        return $return;
    }
}
