<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreItemByList extends FormRequest
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
        return [
            "items" => "required|array",
            "items.*.code" => "sometimes|string|min:4|max:18",
            "items.*.codebar" => "sometimes|nullable|string|min:10|max:100",
            "items.*.cant" => "required|numeric|min:1",
            "items.*.category_id" => "required|numeric|min:1|exists:categories,id",
            "items.*.supplier_id" => "required|numeric|min:1|exists:contacts,id",
            "items.*.brand_id" => "required|numeric|min:1|exists:brands,id",
            "items.*.branch_id" => ["required", "numeric", "min:1", Rule::exists("config", "id")->where("name", "branches")],
            "items.*.name" => "required|string|min:4",
            "items.*.price" => "required|numeric|min:1",
            "items.*.cost" => "sometimes|nullable|numeric|min:0",
            "items.*.invoice" => "required|string|min:3",
        ];
    }
}
