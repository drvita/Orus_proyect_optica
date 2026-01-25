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
            $return['id'] = $this->id;
            $return['name'] = $this->name;
            $return['email'] = $this->email;
            $return['type'] = $this->type;
            $return['age'] = $this->age;
            $return['business'] = $this->business;
            $return['enUso'] = $this->en_uso;

            $return['phones'] = $this->whenLoaded('phones', function () {
                return new PhoneNumberCollection($this->phones);
            });

            if (isset($request->type) && $request->type == 1) {
                $return['brands'] = $this->whenLoaded('brands', function () {
                    return BrandShort::collection($this->brands);
                });
            }

            $return['exams'] = $this->whenLoaded('exams', function () {
                return ExamShort::collection(
                    $this->exams()->with('user')->paginate(10, ['*'], 'exam_page')
                );
            });

            $return['orders'] = $this->whenLoaded('orders', function () {
                return Order::collection(
                    $this->orders()->paginate(10, ['*'], 'order_page')
                );
            });

            $return["metadata"] = $this->whenLoaded('metas', function () {
                return $this->metas->count() ? new Metas($this->metas[0]) : [];
            });

            $return['created'] = $this->whenLoaded('user', function () {
                return new UserSimple($this->user);
            });

            $return['updated'] = $this->whenLoaded('user_updated', function () {
                return new UserSimple($this->user_updated);
            });

            $return['created_at'] = $this->created_at?->format('Y-m-d H:i');
            $return['updated_at'] = $this->updated_at?->format('Y-m-d H:i');
        }

        return $return;
    }
}
