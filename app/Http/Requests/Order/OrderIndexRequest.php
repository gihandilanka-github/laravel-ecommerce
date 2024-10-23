<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderIndexRequest extends FormRequest
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
            'limit' => 'sometimes|integer',
        ];
    }

    public function messages(): array
    {
        return [];
    }
}
