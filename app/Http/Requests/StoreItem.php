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
        $rules = [
            "unit" => "required|nullable|max:4",
            "category_id" => "required|numeric|min:1,exists:categories,id",
        ];

        if ($this->method() === "PUT") {
            $rules["name"] = ["required", "nullable", "max:150", Rule::unique('store_items')->ignore($this->route('store'))];
            $rules["code"] = ["required", "nullable", "max:18", Rule::unique('store_items')->ignore($this->route('store'))];

            if (isset($data['codebar'])) {
                $rules["codebar"] = ["nullable", "max:100", Rule::unique('store_items')->ignore($this->route('store'))];
            }
        } else {
            $rules["name"] = "required|nullable|max:150|unique:store_items";
            $rules["code"] = "required|nullable|max:18|unique:store_items";

            if (isset($data['codebar'])) {
                $rules["codebar"] = "nullable|max:100|unique:store_items";
            }
        }

        if (isset($data['supplier_id'])) {
            $rules["supplier_id"] = "nullable|numeric|min:1|exists:contacts,id";
        }

        if (isset($data['brand_id'])) {
            $rules["brand_id"] = "nullable|numeric|min:1,exists:brands,id";
        }

        return $rules;
    }

    public function attributes()
    {
        return [
            "code" => "codigo del producto",
            "codebar" => "codigo de barras",
            "name" => "nombre del producto",
            "unit" => "unidad de presentación",
            "category_id" => "categoria del producto",
            "supplier_id" => "proveedor"
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
