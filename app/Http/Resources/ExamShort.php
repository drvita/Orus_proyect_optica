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
            $return['esferaoi'] = $this->esferaoi ?? 0;
            $return['esferaod'] = $this->esferaod ?? 0;
            $return['cilindroi'] = $this->cilindroi ?? 0;
            $return['cilindrod'] = $this->cilindrod ?? 0;
            $return['ejeoi'] = $this->ejeoi ?? 0;
            $return['ejeod'] = $this->ejeod ?? 0;
            $return['adicioni'] = $this->adicioni ?? 0;
            $return['adiciond'] = $this->adiciond ?? 0;
            $return['adicion_media_od'] = $this->adicion_media_od ?? 0;
            $return['adicion_media_oi'] = $this->adicion_media_oi ?? 0;
            $return['dpoi'] = $this->dpoi ?? 0;
            $return['dpod'] = $this->dpod ?? 0;
            $return['alturaod'] = $this->alturaod ?? 0;
            $return['alturaoi'] = $this->alturaoi ?? 0;
            $return['lcgod'] = $this->lcgod ?? 0;
            $return['lcgoi'] = $this->lcgoi ?? 0;
            $return['category_ii'] = $this->category_ii ?? null;
            $return['category_id'] = $this->category_id ?? null;

            $return['created_at'] = $this->created_at?->format('Y-m-d H:i');
        }
        return $return;
    }
}
