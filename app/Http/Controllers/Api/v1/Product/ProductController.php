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

    /**
     * Retrieve a list of products.
     *
     * @param  ProductIndexRequest  $request
     * @return ProductCollection
     */
    public function index(ProductIndexRequest $request): ProductCollection
    {
        return new ProductCollection($this->productService->index($request->validated()));
    }

    /**
     * Create a new product.
     *
     * @param  ProductCreateRequest  $request
     * @return ProductResource
     */
    public function store(ProductCreateRequest $request): ProductResource
    {
        return new ProductResource($this->productService->store($request->validated()));
    }

    /**
     * Retrieve a single product.
     *
     * @param  int  $id
     * @return ProductResource
     */
    public function show(int $id): ProductResource
    {
        return new ProductResource($this->productService->show($id));
    }
}
