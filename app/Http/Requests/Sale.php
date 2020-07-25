<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Sale extends FormRequest
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
        return [
            "session" => "required",
            "items" => "required",
            "subtotal" => "required",
            "total" => "required",
            "contact_id" => "required"
        ];
    }
}
