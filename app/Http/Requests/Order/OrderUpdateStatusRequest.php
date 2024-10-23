<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderUpdateStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'order_id' => ['required', 'integer'],
            'status' => ['required', 'status'],
        ];
    }

    public function messages(): array
    {
        return [];
    }
}
