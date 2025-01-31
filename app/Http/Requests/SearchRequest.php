<?php

namespace App\Http\Requests;

use App\Rules\PhoneNumber;
use App\Rules\Recaptcha;
use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
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
            'term' => ['required'],
        ];

        return $rules;
    }

    public function messages()
    {
        return [];
    }
}
