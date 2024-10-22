<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PermissionStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|unique:permissions,name',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
        ];
    }
}
