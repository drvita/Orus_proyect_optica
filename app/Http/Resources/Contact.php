<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use App\Http\Resources\ExamShort as ExamResource;
use App\Http\Resources\SaleInContact as SaleResource;
use App\Http\Resources\BrandShort as BrandResource;
use App\Http\Resources\OrderInExam as OrderResource;

class Contact extends JsonResource
{

    public function toArray($request)
    {

        $return = [];
        $perPage = 10;

        if (isset($this->id)) {
            $edad = $this->birthday !== null ? $this->birthday->diffInYears(carbon::now()) : 0;
            $exams = $this->exams()->with('user')->paginate($perPage, ['*'], 'exam_page');
            $supplierOf = $this->supplier()->paginate($perPage, ['*'], 'suppliers_page');
            $brands = $this->brands()->paginate($perPage, ['*'], 'brands_page');
            $purchases = $this->buys()->paginate($perPage, ['*'], 'purchases_page');
            $orders = $this->orders()->paginate($perPage, ['*'], 'orders_page');

            $return['id'] = $this->id;
            $return['nombre'] = $this->name;
            $return['rfc'] = $this->rfc ?? '';
            $return['email'] = $this->email;
            $return['tipo'] = $this->type;
            $return['empresa'] = $this->business;
            $return['telefonos'] =  is_string($this->telnumbers) ? json_decode($this->telnumbers) : $this->telnumbers;
            $return['f_nacimiento'] = $this->birthday && intval($this->birthday->format('Y')) > 1900 ? $this->birthday->format('Y-m-d') : null;
            $return['edad'] = 1 < $edad && $edad < 120 ? $edad : 0;
            $return['domicilio'] = is_string($this->domicilio) ? json_decode($this->domicilio) : $this->domicilio;

            $return['compras'] = SaleResource::collection($purchases);
            $return['purchases_count'] = $purchases->total();

            $return['marcas'] = BrandResource::collection($brands);
            $return['brands_count'] = $brands->total();

            $return['examenes'] = ExamResource::collection($exams);
            $return['exams_count'] = $exams->total();

            $return['proveedor_de'] = OrderResource::collection($supplierOf);
            $return['suppliers_count'] = $supplierOf->total();

            $return['orders'] = OrderResource::collection($orders);
            $return['orders_count'] = $orders->total();

            $return['enUso'] = $return['purchases_count'] +
                $return['brands_count'] +
                $return['exams_count'] +
                $return['suppliers_count'] +
                $return['orders_count'];


            $return["metadata"] = $this->metas->count() ? new Metas($this->metas) : [];
            $return['created'] = new UserInExam($this->user);
            $return['updated'] = new UserInExam($this->user_updated);
            $return['deleted_at'] = $this->deleted_at ? $this->deleted_at->format('Y-m-d H:i') : null;
            $return['created_at'] = $this->created_at->format('Y-m-d H:i');
            $return['updated_at'] = $this->updated_at->format('Y-m-d H:i');
        }

        return $return;
    }
}