<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Order extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $data = $this->request->all();
        $rules = [];

        if ($this->method() === "POST") {
            $rules['session'] = "required|unique:orders";
            $rules['contact_id'] = "required|exists:contacts,id";
            $rules['exam_id'] = "required|numeric|exists:exams,id";

            $rules['items'] = "required|array";
            $rules['items.*.store_items_id'] = ["required", "numeric", Rule::exists("store_items", "id")->whereNull('deleted_at')];
            $rules['items.*.cant'] = "required|numeric|min:1";
            $rules['items.*.price'] = "required|numeric|min:1";

            if ($this->request->has('sale')) {
                $rules['sale.discount'] = "required|numeric";

                if (array_key_exists("payments", $data['sale'])) {
                    $rules['sale.payments'] = "required|array";

                    $rules['sale.payments.*.metodopago'] = "required|numeric|between:0,6";
                    $rules['sale.payments.*.total'] = "required|numeric|min:1";

                    $rules['sale.payments.*.bank_id'] = "required_unless:sale.payments.*.metodopago, 1,4";
                    $rules['sale.payments.*.auth'] = "required_unless:sale.payments.*.metodopago,1";
                }
            }
        } else if ($this->method() === "PUT") {
            $rules['status'] = "required|numeric|between:0,4";
            // $rules['exam_id'] = "required|numeric|exists:exams,id";

            if ($data['status'] == 1) {
                $rules['lab_id'] = ["sometimes", "numeric", Rule::exists("contacts", "id")->whereNull('deleted_at')->where('type', 1)->where('business', 1)];
                $rules['lab_order'] = "required|string|between:1,100";
            } else if ($data['status'] == 2) {
                $rules['bi_box'] = "sometimes|numeric|min:1";
                $rules['bi_details'] = "sometimes|string";
            }
        }

        return $rules;
    }
    public function attributes()
    {
        return [
            "items" => "productos",
            "session" => "session",
            "contact_id" => "ID del paciente",
            "sale" => "datos de la venta",
            "sale.payments" => "datos de los abonos",
            "sale.payments.*.metodopago" => "metodo de pago",
            "sale.payments.*.total" => "total del abono",
            // "sale.payments.*.bank_id" => "ID del banco",
            "sale.payments.*.auth" => "numero de autorización",
        ];
    }
    public function messages()
    {
        return [
            "items.array" => "Los campos de productos no es valido",
            "items.required" => "El campo de productos 'items' es requerido",
            "session.required" => "El campo session es un valor requerido",
            "session.unique" => "La session ya se encuentra registrada"
        ];
    }
}
