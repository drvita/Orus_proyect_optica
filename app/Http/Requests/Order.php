<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Order extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return match ($this->method()) {
            'POST' => $this->onCreate(),
            'PUT', 'PATCH' => $this->onUpdate(),
            default => [],
        };
    }

    /**
     * Rules for creating an order.
     */
    protected function onCreate(): array
    {
        $version = (int) $this->input('version', 1);
        $isVersion2 = $version >= 2;
        $presence = $isVersion2 ? 'nullable' : 'required';

        $rules = [
            'session' => [$presence, 'unique:orders,session'],
            'contact_id' => ['required', 'exists:contacts,id'],
            'exam_id' => ['required', 'numeric', 'exists:exams,id'],
            'items' => [$presence, 'array'],
            'items.*.store_items_id' => [
                'required',
                'numeric',
                Rule::exists("store_items", "id")->whereNull('deleted_at'),
            ],
            'items.*.cant' => ['required', 'numeric', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:1'],
        ];

        if ($this->has('sale') || $isVersion2) {
            $rules['sale.discount'] = [$presence, 'numeric'];

            if ($this->has('sale.payments') || $isVersion2) {
                $rules['sale.payments'] = [$presence, 'array'];
                $rules['sale.payments.*.metodopago'] = ['required', 'numeric', 'between:0,6'];
                $rules['sale.payments.*.total'] = ['required', 'numeric', 'min:1'];
                $rules['sale.payments.*.bank_id'] = ['required_unless:sale.payments.*.metodopago,1,4'];
                $rules['sale.payments.*.auth'] = ['required_unless:sale.payments.*.metodopago,1'];
            }
        }

        return $rules;
    }

    /**
     * Rules for updating an order.
     */
    protected function onUpdate(): array
    {
        $rules = [
            'status' => ['required', 'numeric', 'between:0,4'],
        ];

        $status = (int) $this->input('status');

        if ($status === 1) {
            $rules['lab_id'] = [
                'sometimes',
                'numeric',
                Rule::exists("contacts", "id")
                    ->whereNull('deleted_at')
                    ->where('type', 1)
                    ->where('business', 1),
            ];
            $rules['lab_order'] = ['required', 'string', 'between:1,100'];
        } elseif ($status === 2) {
            $rules['bi_box'] = ['sometimes', 'numeric', 'min:1'];
            $rules['bi_details'] = ['sometimes', 'string'];
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            "items" => "productos",
            "session" => "session",
            "contact_id" => "ID del paciente",
            "sale" => "datos de la venta",
            "sale.payments" => "datos de los abonos",
            "sale.payments.*.metodopago" => "metodo de pago",
            "sale.payments.*.total" => "total del abono",
            "sale.payments.*.auth" => "numero de autorización",
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            "items.array" => "Los campos de productos no son válidos",
            "items.required" => "El campo de productos 'items' es requerido",
            "session.required" => "El campo session es un valor requerido",
            "session.unique" => "La session ya se encuentra registrada",
        ];
    }
}
