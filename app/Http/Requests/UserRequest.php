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

        if (!$this->route('user')) {
            $rules['name'] = "required";
            $rules['username'] = "required";
            $rules['email'] = [Rule::unique('users')->ignore($this->route('user')), "email"];
            $rules['password'] = "required|min:8";
            $rules['branch_id'] = "required|numeric";
        } else {
            if (array_key_exists("email", $data)) {
                $rules['email'] = [Rule::unique('users')->ignore($this->route('user')), "email"];
            }
            if (array_key_exists("branch_id", $data)) {
                $rules['branch_id'] = "required|numeric";
            }
        }

        return $rules;
    }
}