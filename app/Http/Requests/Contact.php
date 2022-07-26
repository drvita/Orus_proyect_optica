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
        $rules = [];

        switch ($this->method()) {
            case 'PUT':

                if (array_key_exists("email", $data)) {
                    $rules['email'] = [Rule::unique('contacts')->ignore($this->contact->id), "email"];
                }
                if (array_key_exists("birthday", $data)) {
                    $rules['birthday'] = "required|date";
                }
                if (array_key_exists("phones", $data)) {
                    $rules['phones'] = "required|array";
                    $rules['phones.cell'] = "sometimes|nullable|numeric";
                    $rules['phones.notices'] = "sometimes|nullable|numeric";
                    $rules['phones.office'] = "sometimes|nullable|numeric";
                }
                if (array_key_exists("domicilio", $data)) {
                    $rules['domicilio'] = "required|array";
                    $rules['domicilio.street'] = "sometimes|required";
                    $rules['domicilio.neighborhood'] = "sometimes|required";
                    $rules['domicilio.location'] = "sometimes|required";
                    $rules['domicilio.state'] = "sometimes|required";
                    $rules['domicilio.zip'] = "sometimes|required";
                }
                if (array_key_exists("gender", $data)) {
                    $rules['gender'] = "required|string";
                }
                break;
            default:
                $rules['name'] = "required";
                $rules['type'] = "required";
                $rules['email'] = "email|required|unique:contacts";
                $rules['birthday'] = "required|date";
                $rules['phones'] = "required|array";
                $rules['phones.cell'] = "sometimes|nullable|numeric";
                $rules['phones.notices'] = "sometimes|nullable|numeric";
                $rules['phones.office'] = "sometimes|nullable|numeric";
                $rules['gender'] = "required|string";

                // if (array_key_exists("domicilio", $data)) {
                //     $rules['domicilio'] = "required|array";
                //     $rules['domicilio.street'] = "sometimes|required";
                //     $rules['domicilio.neighborhood'] = "sometimes|required";
                //     $rules['domicilio.location'] = "sometimes|required";
                //     $rules['domicilio.state'] = "sometimes|required";
                //     $rules['domicilio.zip'] = "sometimes|required";
                // }
        }
        return $rules;
    }
}
