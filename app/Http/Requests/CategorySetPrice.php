<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategorySetPrice extends FormRequest
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
            "price" => "required|numeric|min:1"
        ];

        if (isset($data["branch_id"]) && $data["branch_id"] > 0) {
            $rules["branch_id"] = ["required", "numeric", Rule::exists("config", "id")->where("name", "branches")];
        } else {
            $rules["branch_id"] = "required|numeric|min:0";
        }

        return $rules;
    }
}
