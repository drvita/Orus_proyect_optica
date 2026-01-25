<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use App\Http\Resources\ExamShort as ExamResource;
use App\Http\Resources\BrandShort as BrandResource;
use App\Http\Resources\OrderInExam as OrderResource;

class Contact extends JsonResource
{

    public function toArray($request)
    {
        $return = [];

        if (isset($this->id)) {
            $exams = $this->whenLoaded('exams');
            $supplierOf = $this->whenLoaded('supplier');
            $brands = $this->whenLoaded('brands');
            $purchases = $this->whenLoaded('buys');
            $orders = $this->whenLoaded('orders');

            $metas = $this->whenLoaded('metas');

            $metadata = null;
            $activity = collect();

            if (!($metas instanceof \Illuminate\Http\Resources\MissingValue)) {
                $metadata = $metas->where('key', 'metadata')->first();
                $activity = $metas->whereIn('key', ['updated', 'deleted', 'created'])->take(25);
            }

            // Fallback for activity manually added in previous version
            $obj = [
                'id' => 0,
                'key' => 'created',
                'value' => json_encode([
                    "datetime" => $this->created_at,
                    "user_id" => $this->user->id
                ])
            ];
            $obj = json_decode(json_encode($obj), false);
            $obj->value = json_decode($obj->value, true);
            $activity->push($obj);

            $return['id'] = $this->id;
            $return['name'] = $this->name;
            $return['rfc'] = $this->rfc ?? '';
            $return['email'] = $this->email;
            $return['type'] = $this->type;
            $return['business'] = $this->business;
            $return['age'] = $this->age;

            $return['phones'] = $this->whenLoaded('phones', function () {
                $allPhones = $this->phones;
                $movil = $allPhones->whereIn('type', ['whatsapp', 'movil'])->first();
                $others = $allPhones->whereNotIn('type', ['whatsapp', 'movil'])->first();

                return [
                    'cell' => $movil?->number ?? '',
                    'office' => $others?->number ?? '',
                    'notices' => ''
                ];
            });
            $return['address'] = new ContactAddress($this->domicilio);

            $isPaginator = fn($value) => $value instanceof \Illuminate\Pagination\LengthAwarePaginator;

            // Conditional rendering for paginated relations
            $return['purchases'] = $this->when(!($purchases instanceof \Illuminate\Http\Resources\MissingValue), function () use ($purchases) {
                return SaleInContact::collection($purchases);
            });
            $return['purchases_count'] = $isPaginator($purchases) ? $purchases->total() : ($this->buys_count ?? 0);

            $return['brands'] = $this->when(!($brands instanceof \Illuminate\Http\Resources\MissingValue), function () use ($brands) {
                return BrandResource::collection($brands);
            });
            $return['brands_count'] = $isPaginator($brands) ? $brands->total() : ($this->brands_count ?? 0);

            $return['exams'] = $this->when(!($exams instanceof \Illuminate\Http\Resources\MissingValue), function () use ($exams) {
                return ExamResource::collection($exams);
            });
            $return['exams_count'] = $isPaginator($exams) ? $exams->total() : ($this->exams_count ?? 0);

            $return['supplier_of'] = $this->when(!($supplierOf instanceof \Illuminate\Http\Resources\MissingValue), function () use ($supplierOf) {
                return OrderResource::collection($supplierOf);
            });
            $return['suppliers_count'] = $isPaginator($supplierOf) ? $supplierOf->total() : ($this->supplier_count ?? 0);

            $return['orders'] = $this->when(!($orders instanceof \Illuminate\Http\Resources\MissingValue), function () use ($orders) {
                return OrderResource::collection($orders);
            });
            $return['orders_count'] = $isPaginator($orders) ? $orders->total() : ($this->orders_count ?? 0);

            $return["metadata"] = $metadata ? new Metas($metadata) : new \stdClass;
            $return["activity"] = MetasDetails::collection($activity);
            $return['created'] = new UserSimple($this->user);
            $return['updated'] = new UserSimple($this->user_updated);

            $return['deleted_at'] = $this->deleted_at ? $this->deleted_at->format('Y-m-d H:i') : null;
            $return['created_at'] = $this->created_at ? $this->created_at->format('Y-m-d H:i') : null;
            $return['updated_at'] = $this->updated_at ? $this->updated_at->format('Y-m-d H:i') : null;
        }

        return $return;
    }
}
