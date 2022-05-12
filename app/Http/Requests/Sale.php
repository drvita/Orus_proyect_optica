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
            "subtotal" => "required|numeric",
            "total" => "required|numeric",
            "contact_id" => ["required", "numeric", Rule::exists("contacts", "id")],
            // "branch_id" => ["required", "numeric", Rule::exists("config", "id")->where("name", "branches")]
        ];

        if ($this->method() === "PUT") {
            $rules['session'] = ["required", "string", Rule::unique('sales')->ignore($this->sale['id'])];
        } else {
            $rules['session'] = ["required", "string", Rule::unique('sales')];
        }

        if (array_key_exists("payments", $data)) {
            $rules['payments'] = "required|array";
        }
        if (array_key_exists("items", $data)) {
            $rules['items'] = "required|array";
        }
        if (array_key_exists("discount", $data)) {
            $rules['discount'] = "required|numeric";
        }

        return $rules;
    }
}
