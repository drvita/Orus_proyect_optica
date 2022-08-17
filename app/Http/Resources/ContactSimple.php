<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use App\Http\Resources\ExamShort as ExamResource;
use App\Http\Resources\SaleInContact as SaleResource;
use App\Http\Resources\BrandShort as BrandResource;
use App\Http\Resources\OrderInExam as OrderResource;

class ContactSimple extends JsonResource
{

    public function toArray($request)
    {

        $return = [];

        if (isset($this->id)) {

            if ($this->metas && $this->metas->count()) {
                foreach ($this->metas as $meta) {
                    if ($meta->key === "metadata" && isset($meta->value["birthday"])) {
                        $this->birthday = new Carbon($meta->value["birthday"]);
                    }
                }
            }

            $edad = $this->birthday !== null ? $this->birthday->diffInYears(carbon::now()) : 0;

            $return['id'] = $this->id;
            $return['name'] = $this->name;
            $return['email'] = $this->email;
            $return['type'] = $this->type;
            $return['birthday'] = $this->birthday && intval($this->birthday->format('Y')) > 1900 ? $this->birthday->format('Y-m-d') : null;
            $return['age'] = $this->edad ? $this->edad : $edad;
            $return['phones'] =  new ContactPhones($this->telnumbers);
            $return['created_at'] = $this->created_at->format('Y-m-d H:i');

            if ($edad <= 0 || $edad > 150) {
                $return['birthday'] = null;
                $return['age'] = 0;
            }
        }

        return $return;
    }
}
