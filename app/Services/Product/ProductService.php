<?php

namespace App\Services\Product;

use App\Repositories\Product\ProductRepository;
use Illuminate\Support\Arr;

class ProductService
{
    public function __construct(protected ProductRepository  $productRepository) {}

    public function index(array $request)
    {
        $productCacheListTag = config('constants.product.default_cache_tag_prefix');

        if (!empty($request['limit'])) {
            $productCacheListTag = $productCacheListTag . 'ProductListPaginated';
        }

        $cacheKey = generateCacheKey(Arr::only($request, ['name']));
        $cacheData = getCache($productCacheListTag, $cacheKey);

        if ($cacheData) {
            logger()->info('ProductList: get data from cache', [$productCacheListTag, $cacheKey]);
            return $cacheData;
        }

        logger()->info('ProductList: get data from database');
        $products = $this->productRepository->index($request);
        putCache($productCacheListTag, $cacheKey, $products, config('constants.product.default_cache_time'));

        return $products;
    }

    public function store(array $request)
    {
        return $this->productRepository->create($request);
    }

    public function ensureUniqueSlug(string $slug, $id = null): string
    {
        return $this->productRepository->ensureUniqueSlug($slug, $id);
    }
}
