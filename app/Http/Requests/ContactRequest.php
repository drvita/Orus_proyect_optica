<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('name')) {
            $this->merge([
                'name' => strtolower(normaliza($this->input('name')))
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [];

        switch ($this->method()) {
            case 'PUT':
                $rules['email'] = ['sometimes', 'email', Rule::unique('contacts')->ignore($this->contact->id)];
                $rules['birthday'] = ['sometimes', 'date'];
                $rules['phones'] = ['sometimes', 'array'];
                $rules['phones.cell'] = ['sometimes', 'nullable', 'numeric'];
                $rules['phones.notices'] = ['sometimes', 'nullable', 'numeric'];
                $rules['phones.office'] = ['sometimes', 'nullable', 'numeric'];
                $rules['domicilio'] = ['sometimes', 'array'];
                $rules['domicilio.street'] = ['sometimes', 'required'];
                $rules['domicilio.neighborhood'] = ['sometimes', 'required'];
                $rules['domicilio.location'] = ['sometimes', 'required'];
                $rules['domicilio.state'] = ['sometimes', 'required'];
                $rules['domicilio.zip'] = ['sometimes', 'required'];
                $rules['gender'] = ['sometimes', 'string'];
                $rules['name'] = ['sometimes'];
                break;
            default:
                $rules['name'] = ['required'];
                $rules['type'] = ['required'];
                $rules['email'] = ['required', 'email', 'unique:contacts'];
                $rules['birthday'] = ['required', 'date'];
                $rules['phones'] = ['required', 'array'];
                $rules['phones.cell'] = ['sometimes', 'nullable', 'numeric'];
                $rules['phones.notices'] = ['sometimes', 'nullable', 'numeric'];
                $rules['phones.office'] = ['sometimes', 'nullable', 'numeric'];
                $rules['gender'] = ['required', 'string'];
                
                // Optional domicilio validation
                $rules['domicilio'] = ['sometimes', 'array'];
                $rules['domicilio.street'] = ['sometimes', 'required'];
                $rules['domicilio.neighborhood'] = ['sometimes', 'required'];
                $rules['domicilio.location'] = ['sometimes', 'required'];
                $rules['domicilio.state'] = ['sometimes', 'required'];
                $rules['domicilio.zip'] = ['sometimes', 'required'];
        }
        return $rules;
    }
}
