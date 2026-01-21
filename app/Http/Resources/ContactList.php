<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ContactList extends JsonResource
{

    public function toArray($request)
    {

        $return = [];
        if (isset($this->id)) {
            $birthday = $this->birthday;
            if (!$birthday && $this->metas && $this->metas->count()) {
                foreach ($this->metas as $meta) {
                    if ($meta->key === "metadata" && isset($meta->value["birthday"])) {
                        $birthday = new Carbon($meta->value["birthday"]);
                    }
                }
            }
            if (is_string($birthday)) {
                $birthday = Carbon::parse($birthday);
            }
            $edad = $birthday !== null ? (int)$birthday->diffInYears(Carbon::now()) : 0;
            // $exams = $this->exams()->with('user')->paginate(10, ['*'], 'exam_page');

            $return['id'] = $this->id;
            $return['name'] = $this->name;
            $return['email'] = $this->email;
            $return['type'] = $this->type;
            $return['phones'] =  new ContactPhones($this->telnumbers);
            $return['age'] = $edad > 0 && $edad < 120 ? $edad : 0;
            $return['business'] = $this->business;

            $return['enUso'] = count($this->buys) +
                count($this->brands) +
                count($this->exams) +
                count($this->supplier) +
                count($this->orders);

            // $return['exams'] = ExamShort::collection($exams);
            $return["metadata"] = $this->metas->count() ? new Metas($this->metas[0]) : [];
            $return['created'] = new UserSimple($this->user);
            $return['updated'] = new UserSimple($this->user_updated);

            $return['created_at'] = $this->created_at ? $this->created_at->format('Y-m-d H:i') : null;
            $return['updated_at'] = $this->updated_at ? $this->updated_at->format('Y-m-d H:i') : null;

            if (isset($request->type) && $request->type == 1) {
                $return['brands'] = BrandShort::collection($this->brands);
            }
        }


        return $return;
        // return parent::toArray($request);
    }
}
