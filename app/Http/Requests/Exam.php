<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Exam extends FormRequest
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
            "contact_id" => ["required", "numeric", Rule::exists("contacts", "id")],
            "category_id" => ["nullable", "numeric", Rule::exists("categories", "id")],
            "category_ii" => ["nullable", "numeric", Rule::exists("categories", "id")],
            "age" => ["nullable", "numeric"],
        ];

        return $rules;
    }
}
