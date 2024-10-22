<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RoleIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sortOrder' => 'sometimes|in:asc,desc',
            'sortBy' => 'sometimes|in:id,name,email,created_at',
            'name' => 'sometimes|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [];
    }
}
