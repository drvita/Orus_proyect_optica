<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class UserRequest extends FormRequest
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
                    $rules['email'] = [Rule::unique('users')->ignore($this->route('user')), "email"];
                }
                if (array_key_exists("branch_id", $data)) {
                    $rules['branch_id'] = ["numeric", Rule::exists("config", "id")->where("name", "branches")];
                }
                if (array_key_exists("password", $data)) {
                    $rules['password'] = "min:8";
                }
                if (array_key_exists("role", $data)) {
                    $rules['role'] = "exists:roles,name";
                }
                break;
            default:
                $rules['name'] = "required";
                $rules['username'] = "required";
                $rules['email'] = "email|required|unique:users";
                $rules['password'] = "required|min:8";
                $rules['role'] = "required|exists:roles,name";
        }

        return $rules;
    }
}
