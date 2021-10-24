<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $data = $this->request->all();
        $ruleCodeBar = ["max:100"];

        if ($data['codebar']) {
            $ruleCodeBar = Rule::unique('store_items')->ignore($this->route('store'));
        }

        // dd($ruleCodeBar);
        return [
            "code" => [
                "required", "max:18",
                Rule::unique('store_items')->ignore($this->route('store'))
            ],
            "codebar" => $ruleCodeBar,
            "name" => ["required", "max:150", Rule::unique('store_items')->ignore($this->route('store'))],
            "unit" => "required|max:4",
            "category_id" => "required"
        ];
    }

    public function attributes()
    {
        return [
            "code" => "codigo del producto",
            "codebar" => "codigo de barras",
            "name" => "nombre del producto",
            "unit" => "unidad de presentación",
            "category_id" => "categoria del producto"
        ];
    }
    public function messages()
    {
        return [
            "code.max" => "El codigo del producto no debe de ser mayor a 18 caracteres",
            "name.max" => "El nombre del producto no debe de ser mayor a 150 caracteres",
            "code.unique" => "El codigo ya se encuentra registrado en otro producto",
            "codebar.unique" => "El codigo de barras ya se encuentra registrado en otros productos"
        ];
    }
}