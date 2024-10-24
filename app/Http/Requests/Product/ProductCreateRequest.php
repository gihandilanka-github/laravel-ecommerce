<?php

namespace App\Http\Requests\Product;

use App\Services\Product\ProductService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class ProductCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|unique:products,slug',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [];
    }

    protected function prepareForValidation()
    {
        if (!$this->slug) {
            $productService = app(ProductService::class);
            $this->merge([
                'slug' => $productService->ensureUniqueSlug(Str::slug($this->name)),
            ]);
        }
    }

    protected function failedValidation(Validator $validator)
    {
        $response = [
            'data'         => null,
            'errors'       => $validator->errors()
        ];

        logger()->error(json_encode($response));

        throw new HttpResponseException(response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
