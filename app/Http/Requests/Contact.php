<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Contact extends FormRequest
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
            "name" => "required",
            "type" => "required",
        ];

        switch ($this->method()) {
            case 'PUT':

                if (array_key_exists("email", $data)) {
                    $rules['email'] = [Rule::unique('contacts')->ignore($this->route('user')), "email"];
                }
                if (array_key_exists("birthday", $data)) {
                    $rules['birthday'] = "required|date";
                }
                if (array_key_exists("telnumbers", $data)) {
                    $rules['telnumbers'] = "required|array";
                }
                if (array_key_exists("domicilio", $data)) {
                    $rules['domicilio'] = "required|array";
                }
                if (array_key_exists("gender", $data)) {
                    $rules['gender'] = "required|string";
                }
                break;
            default:
                $rules['email'] = "email|required|unique:contacts";
                $rules['birthday'] = "required|date";
                $rules['telnumbers'] = "required|array";
                if (array_key_exists("domicilio", $data)) {
                    $rules['domicilio'] = "required|array";
                }
                if (array_key_exists("gender", $data)) {
                    $rules['gender'] = "required|string";
                }
        }
        return $rules;
    }
}
