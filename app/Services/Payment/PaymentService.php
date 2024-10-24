<?php

namespace App\Services\Payment;

use App\Repositories\Payment\PaymentRepository;
use Illuminate\Support\Arr;

class PaymentService
{
    public function __construct(protected PaymentRepository  $paymentRepository) {}

    public function index(array $request)
    {
        $paymentCacheListTag = config('constants.payment.default_cache_tag_prefix');

        if (!empty($request['limit'])) {
            $paymentCacheListTag = $paymentCacheListTag . 'PaymentListPaginated';
        }

        $cacheKey = generateCacheKey(Arr::only($request, ['transaction_id']));
        $cacheData = getCache($paymentCacheListTag, $cacheKey);

        if ($cacheData) {
            logger()->info('PaymentList: get data from cache', [$paymentCacheListTag, $cacheKey]);
            return $cacheData;
        }

        logger()->info('PaymentList: get data from database');
        $payments = $this->paymentRepository->index($request);
        putCache($paymentCacheListTag, $cacheKey, $payments, config('constants.payment.default_cache_time'));

        return $payments;
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
