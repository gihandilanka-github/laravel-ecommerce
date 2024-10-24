<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Repositories\Product\ProductRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class ProductService
{
    public function __construct(protected ProductRepository  $productRepository) {}

    /**
     * Retrieve a list of products with optional caching.
     *
     * @param array $request
     * @return Collection|LengthAwarePaginator
     */
    public function index(array $request): Collection|LengthAwarePaginator
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

    /**
     * Create a new product.
     *
     * @param array $request
     * @return Product
     */
    public function store(array $request): Product
    {
        return $this->productRepository->create($request);
    }

    /**
     * Generate a unique slug, given a string and an optional ID.
     *
     * @param string $slug
     * @param int|null $id
     * @return string
     */
    public function ensureUniqueSlug(string $slug, $id = null): string
    {
        return $this->productRepository->ensureUniqueSlug($slug, $id);
    }

    /**
     * Retrieve a product by its ID.
     *
     * @param int $id
     * @return Product
     */
    public function show(int $id): Product
    {
        return $this->productRepository->show($id);
    }
}
