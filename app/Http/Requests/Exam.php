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
        ];

        if (array_key_exists("category_id", $data)) {
            $rules['category_id'] = ["required", "numeric", Rule::exists("categories", "id")];
        }
        if (array_key_exists("category_ii", $data)) {
            $rules['category_ii'] = ["required", "numeric", Rule::exists("categories", "id")];
        }

        return $rules;
    }
}
