<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Sale extends FormRequest
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
        $rules = [
            "contact_id" => ["required", "numeric", Rule::exists("contacts", "id")->whereNull('deleted_at')],
            "discount" => "sometimes|numeric",
        ];

        if ($this->method() === "PUT") {
            if (isset($data["session"])) {
                $rules['session'] = ["required", "string", Rule::unique('sales')->ignore($this->sale['id'])];
            }

            if (isset($data["items"])) {
                $rules['items'] = "array";

                if (count($data['items'])) {
                    $rules['items.*.cant'] = "required|numeric";
                    $rules['items.*.price'] = "required|numeric|min:1";
                    $rules['items.*.store_items_id'] = ["required", "numeric", Rule::exists("store_items", "id")->whereNull('deleted_at')];
                }
            }
        } else {
            $rules['session'] = ["required", "string", Rule::unique('sales')];
            $rules['items'] = "required|array";
            $rules['items.*.cant'] = "required|numeric";
            $rules['items.*.price'] = "required|numeric|min:1";
            $rules['items.*.store_items_id'] = ["required", "numeric", Rule::exists("store_items", "id")->whereNull('deleted_at')];

            if (isset($data["payments"])) {
                $rules['payments'] = "array";

                if (count($data['items'])) {
                    $rules['payments.*.id'] = "required";
                    $rules['payments.*.metodopago'] = "required|numeric|between:0,6";
                    $rules['payments.*.total'] = "required|numeric|min:1";
                    $rules['payments.*.bank_id'] = "required_unless:payments.*.metodopago, 1,4";
                    $rules['payments.*.auth'] = "required_unless:payments.*.metodopago,1";
                }
            }
        }

        return $rules;
    }
}
