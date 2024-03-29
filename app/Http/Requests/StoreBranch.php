<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBranch extends FormRequest
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
            "cant" => "required|numeric",
            "price" => "required|numeric",
            "store_item_id" => "required|numeric|exists:store_items,id",
            "branch_id" => ["required", "numeric", Rule::exists("config", "id")->where("name", "branches")]
        ];
    }
}
