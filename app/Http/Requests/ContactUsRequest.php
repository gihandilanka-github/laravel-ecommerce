<?php

namespace App\Http\Requests;

use App\Rules\PhoneNumber;
use App\Rules\Recaptcha;
use Illuminate\Foundation\Http\FormRequest;

class ContactUsRequest extends FormRequest
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
        $rules = [
            'name' => ['required'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', new PhoneNumber()],
            'message' => ['required'],
            'recaptcha' => ['required', new Recaptcha()]
        ];

        return $rules;
    }

    public function messages()
    {
        return [];
    }
}
