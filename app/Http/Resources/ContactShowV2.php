<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ExamShort as ExamResource;
use App\Http\Resources\OrderInExam as OrderResource;

class ContactShowV2 extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'rfc' => $this->rfc ?? '',
            'email' => $this->email,
            'type' => $this->type,
            'business' => $this->business,
            'age' => $this->age,
            'phones' => new PhoneNumberCollection($this->whenLoaded('phones')),
            'address' => new ContactAddress($this->domicilio),
            'exams' => ExamResource::collection($this->whenLoaded('exams')),
            'orders' => OrderResource::collection($this->whenLoaded('orders')),
            'metadata' => $this->whenLoaded('metas', function () {
                $meta = $this->metas->where('key', 'metadata')->first();
                return $meta ? new Metas($meta) : new \stdClass;
            }),
            'created' => new UserSimple($this->whenLoaded('user')),
            'updated' => new UserSimple($this->whenLoaded('user_updated')),
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i') : null,
        ];
    }
}
