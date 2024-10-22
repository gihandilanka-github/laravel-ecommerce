<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:roles,name,' . $this->route('role')->id,
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
        ];
    }
}
