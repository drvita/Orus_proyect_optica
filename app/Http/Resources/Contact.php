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
        $perPage = 10;

        if (isset($this->id)) {
            if ($this->metas && $this->metas->count()) {
                foreach ($this->metas as $meta) {
                    if ($meta->key === "metadata" && isset($meta->value["birthday"])) {
                        $this->birthday = new Carbon($meta->value["birthday"]);
                    }
                }
            }

            $edad = $this->birthday !== null ? $this->birthday->diffInYears(carbon::now()) : 0;
            $exams = $this->exams()->with('user')->paginate($perPage, ['*'], 'exam_page');
            $supplierOf = $this->supplier()->paginate($perPage, ['*'], 'suppliers_page');
            $brands = $this->brands()->paginate($perPage, ['*'], 'brands_page');
            $purchases = $this->buys()->paginate($perPage, ['*'], 'purchases_page');
            $orders = $this->orders()->paginate($perPage, ['*'], 'orders_page');
            $metadata = $this->metas()
                ->where("key", "metadata")
                ->take(25)
                ->get();
            $activity = $this->metas()
                ->where("key", ["updated", "deleted", "created"])
                ->orderBy("id", "desc")
                ->take(25)
                ->get();

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
            $return['age'] = 1 < $edad && $edad < 120 ? $edad : 0;

            $return['phones'] = new ContactPhones($this->telnumbers);
            $return['address'] = new ContactAddress($this->domicilio);

            $return['purchases'] = SaleInContact::collection($purchases);
            $return['purchases_count'] = $purchases->total();

            $return['brands'] = BrandResource::collection($brands);
            $return['brands_count'] = $brands->total();

            $return['exams'] = ExamResource::collection($exams);
            $return['exams_count'] = $exams->total();

            $return['supplier_of'] = OrderResource::collection($supplierOf);
            $return['suppliers_count'] = $supplierOf->total();

            $return['orders'] = OrderResource::collection($orders);
            $return['orders_count'] = $orders->total();

            $return["metadata"] = $metadata->count() ? new Metas($metadata[0]) : new \stdClass;
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
