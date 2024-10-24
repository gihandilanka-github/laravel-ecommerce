<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Repositories\Order\OrderRepository;
use App\Models\User;
use Illuminate\Support\Arr;

class OrderService
{
    public function __construct(protected OrderRepository  $orderRepository) {}

    public function index(array $request)
    {
        $orderCacheListTag = config('constants.order.default_cache_tag_prefix');

        if (!empty($request['limit'])) {
            $orderCacheListTag = $orderCacheListTag . 'OrderListPaginated';
        }

        $cacheKey = generateCacheKey(Arr::only($request, ['name']));
        $cacheData = getCache($orderCacheListTag, $cacheKey);

        if ($cacheData) {
            logger()->info('OrderList: get data from cache', [$orderCacheListTag, $cacheKey]);
            return $cacheData;
        }

        logger()->info('OrderList: get data from database');
        $orders = $this->orderRepository->index($request);
        putCache($orderCacheListTag, $cacheKey, $orders, config('constants.order.default_cache_time'));

        return $orders;
    }

    public function createOrder(User $user, array $request)
    {
        return $this->orderRepository->createOrder($user, $request);
    }

    public function updateOrderStatus(int $orderId, string $status)
    {
        return $this->orderRepository->updateOrderStatus($orderId, $status);
    }
}
