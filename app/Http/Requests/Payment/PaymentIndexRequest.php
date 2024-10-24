<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class PaymentIndexRequest extends FormRequest
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
            'transaction_id' => 'sometimes|string|max:255',
            'limit' => 'sometimes|integer',
        ];
    }

    public function messages(): array
    {
        return [];
    }
}
