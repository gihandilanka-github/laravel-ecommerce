<?php

namespace App\Services\Product;

use App\Repositories\Product\ProductRepository;

class ProductService
{
    public function __construct(protected ProductRepository  $productRepository) {}

    public function index(array $request)
    {
        return $this->productRepository->index($request);
    }

    public function store(array $request)
    {
        return $this->productRepository->create($request);
    }
}
