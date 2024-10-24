<?php

namespace App\Services\Payment;

use App\Repositories\Payment\PaymentRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class PaymentService
{
    public function __construct(protected PaymentRepository  $paymentRepository) {}

    /**
     * Retrieve a list of payments with optional filters and sorting.
     *
     * @param array $request
     * @return Collection|LengthAwarePaginator
     */
    public function index(array $request): Collection|LengthAwarePaginator
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

    /**
     * Create a new payment record.
     *
     * @param array $request
     * @return \App\Models\Payment
     */
    public function store(array $request)
    {
        return $this->paymentRepository->create($request);
    }

    /**
     * Generate a unique slug, given a string and an optional ID.
     *
     * @param string $slug
     * @param int|null $id
     * @return string
     */

    /**
     * Retrieve a single payment by its ID.
     *
     * @param int $id
     * @return \App\Models\Payment
     */
    public function show(int $id)
    {
        return $this->paymentRepository->show($id);
    }
}
