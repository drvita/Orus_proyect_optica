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
            $return['esferaoi'] = $this->esferaoi;
            $return['esferaod'] = $this->esferaod;
            $return['cilindroi'] = $this->cilindroi;
            $return['cilindrod'] = $this->cilindrod;
            $return['ejeoi'] = $this->ejeoi;
            $return['ejeod'] = $this->ejeod;
            $return['adicioni'] = $this->adicioni;
            $return['adiciond'] = $this->adiciond;
            $return['dpoi'] = $this->dpoi;
            $return['dpod'] = $this->dpod;
            $return['alturaoi'] = $this->alturaoi;
            $return['alturaod'] = $this->alturaod;
            $return['adicion_media_od'] = $this->adicion_media_od ?? 0;
            $return['adicion_media_oi'] = $this->adicion_media_oi ?? 0;
            $return['lcmarca'] = $this->lcmarca;
            $return['lcgod'] = $this->lcgod;
            $return['lcgoi'] = $this->lcgoi;
            $return['observaciones'] = $this->observaciones ?? "";
            $return['category_id'] = $this->category_id;
            $return['category_ii'] = $this->category_ii;
            $return['status'] = $this->status;

            $return['created'] = new UserResource($this->user);
            $return['created_at'] = $this->created_at->format('Y-m-d H:i');
        }
        return $return;
    }
}