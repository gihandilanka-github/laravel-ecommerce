<?php

namespace App\Http\Controllers\Api\v1\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductCreateRequest;
use App\Http\Requests\Product\ProductIndexRequest;
use App\Http\Resources\Product\ProductCollection;
use App\Http\Resources\Product\ProductResource;
use App\Services\Product\ProductService;

class ProductController extends Controller
{
    public function __construct(protected ProductService $productService) {}

    public function index(ProductIndexRequest $request)
    {
        return new ProductCollection($this->productService->index($request->validated()));
    }

    public function store(ProductCreateRequest $request)
    {
        return new ProductResource($this->productService->store($request->validated()));
    }

    public function show(int $id)
    {
        return new ProductResource($this->productService->show($id));
    }
}
