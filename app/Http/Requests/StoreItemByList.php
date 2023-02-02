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
            "items.*.id" => ["required", "numeric", Rule::exists("store_items", "id")->whereNull('deleted_at')],
            "items.*.cant" => "required|numeric|min:1",
            "items.*.price" => "nullable|numeric|min:1",
            "items.*.cost" => "sometimes|nullable|numeric|min:0",
            "items.*.branch_id" => ["required", "numeric", Rule::exists("config", "id")->where("name", "branches")],
            "items.*.invoice" => "required|string",
        ];
    }
}
