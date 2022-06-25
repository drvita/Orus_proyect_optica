<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExamShort extends JsonResource
{

    public function toArray($request)
    {
        $return = [];

        if (isset($this->id)) {
            $return['id'] = $this->id;
            $return['status'] = $this->status;
            $return['esferaoi'] = $this->esferaoi ? $this->esferaoi : 0;
            $return['esferaod'] = $this->esferaod ? $this->esferaod : 0;
            $return['cilindroi'] = $this->cilindroi ? $this->cilindroi : 0;
            $return['cilindrod'] = $this->cilindrod ? $this->cilindrod : 0;
            $return['ejeoi'] = $this->ejeoi ? $this->ejeoi : 0;
            $return['ejeod'] = $this->ejeod ? $this->ejeod : 0;
            $return['adicioni'] = $this->adicioni ? $this->adicioni : 0;
            $return['adiciond'] = $this->adiciond ? $this->adiciond : 0;
            $return['adicion_media_od'] = $this->adicion_media_od ? $this->adicion_media_od : 0;
            $return['adicion_media_oi'] = $this->adicion_media_oi ? $this->adicion_media_oi : 0;
            $return['dpoi'] = $this->dpoi ? $this->dpoi : 0;
            $return['dpod'] = $this->dpod ? $this->dpod : 0;
            $return['created_at'] = $this->created_at->format('Y-m-d H:i');
        }
        return $return;
    }
}
