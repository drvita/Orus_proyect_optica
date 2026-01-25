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
                $rules['email'] = ['nullable', 'email', Rule::unique('contacts')->ignore($this->contact->id)];
                $rules['birthday'] = ['nullable', 'date'];
                $rules['phones'] = ['nullable', 'array'];
                $rules['gender'] = ['nullable', 'string'];
                $rules['name'] = ['nullable'];
                break;
            default:
                $rules['name'] = ['required'];
                $rules['type'] = ['required'];
                $rules['email'] = ['nullable', 'email', 'unique:contacts'];
                $rules['birthday'] = ['nullable', 'date', 'after_or_equal:1900-01-01', 'before_or_equal:today'];
                $rules['phones'] = ['required', 'array'];
                $rules['gender'] = ['nullable', 'string', 'in:male,female,other'];
        }

        // Optional domicilio validation
        $rules['domicilio'] = ['nullable', 'array'];
        $rules['domicilio.street'] = ['nullable', 'sometimes'];
        $rules['domicilio.neighborhood'] = ['nullable', 'sometimes'];
        $rules['domicilio.location'] = ['nullable', 'sometimes'];
        $rules['domicilio.state'] = ['nullable', 'sometimes'];
        $rules['domicilio.zip'] = ['nullable', 'sometimes'];
        return $rules;
    }
}
