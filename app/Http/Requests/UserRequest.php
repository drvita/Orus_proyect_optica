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
        $isPut = $this->isMethod('PUT');

        return [
            'name' => [$isPut ? 'sometimes' : 'required', 'string'],
            'username' => [$isPut ? 'sometimes' : 'required', 'string'],
            'email' => [
                $isPut ? 'sometimes' : 'required',
                'email',
                Rule::unique('users')->ignore($this->route('user'))
            ],
            'password' => [$isPut ? 'sometimes' : 'required', 'min:8'],
            'role' => [$isPut ? 'sometimes' : 'required', 'exists:roles,name'],
            'branch_id' => [
                'sometimes',
                'numeric',
                Rule::exists("config", "id")->where("name", "branches")
            ],
            'phones' => 'sometimes|array',
            'phones.*.number' => 'required_with:phones|string',
            'phones.*.type' => 'nullable|string',
            'phones.*.country_code' => 'nullable|string',
        ];
    }
}
