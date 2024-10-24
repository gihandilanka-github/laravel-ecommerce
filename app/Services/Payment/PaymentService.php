<?php

namespace App\Services\Payment;

use App\Repositories\Product\PaymentRepository;
use Illuminate\Support\Arr;

class PaymentService
{
    public function __construct(protected PaymentRepository  $paymentRepository) {}

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
        $products = $this->paymentRepository->index($request);
        putCache($productCacheListTag, $cacheKey, $products, config('constants.product.default_cache_time'));

        return $products;
    }

    public function store(array $request)
    {
        return $this->paymentRepository->create($request);
    }

    public function ensureUniqueSlug(string $slug, $id = null): string
    {
        return $this->paymentRepository->ensureUniqueSlug($slug, $id);
    }

    public function show(int $id)
    {
        return $this->paymentRepository->show($id);
    }
}
