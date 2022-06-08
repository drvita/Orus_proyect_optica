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
            "category_id" => "required|nullable|numeric|exists:categories,id",
        ];

        if ($this->method() === "PUT") {
            $rules["name"] = ["required", "nullable", "max:150", Rule::unique('store_items')->ignore($this->route('store'))];
            $rules["code"] = ["required", "nullable", "max:18", Rule::unique('store_items')->ignore($this->route('store'))];

            if (array_key_exists("codebar", $data)) {
                $rules["codebar"] = ["nullable", "max:100", Rule::unique('store_items')->ignore($this->route('store'))];
            }
        } else {
            $rules["name"] = "required|nullable|max:150|unique:store_items";
            $rules["code"] = "required|nullable|max:18|unique:store_items";

            if (array_key_exists("codebar", $data)) {
                $rules["codebar"] = "nullable|max:100|unique:store_items";
            }
        }

        if (array_key_exists("supplier_id", $data)) {
            $rules["supplier_id"] = "nullable|numeric|exists:contacts,id";
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
