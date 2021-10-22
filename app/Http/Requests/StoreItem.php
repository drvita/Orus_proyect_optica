<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItem extends FormRequest
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
            "code" => "required|unique:store_items|max:18",
            "name" => "required|max:150",
            "unit" => "required|max:4",
            "category_id" => "required"
        ];
    }

    public function attributes()
    {
        return [
            "code" => "codigo del producto",
            "name" => "mombre del producto",
            "unit" => "unidad de presentación",
            "category_id" => "categoria del producto"
        ];
    }
    public function messages()
    {
        return [
            "code.max" => "El codigo del producto no debe de ser mayor a 18 caracteres",
            "name.max" => "El nombre del producto no debe de ser mayor a 150 caracteres"
        ];
    }
}