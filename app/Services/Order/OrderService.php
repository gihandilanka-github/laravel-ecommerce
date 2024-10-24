<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Repositories\Order\OrderRepository;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class OrderService
{
    public function __construct(protected OrderRepository  $orderRepository) {}

    /**
     * Get a list of orders.
     *
     * @param  array  $request
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index(array $request): Collection
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

    /**
     * Create a new order.
     *
     * @param  User  $user
     * @param  array  $request
     *
     * @return \App\Models\Order
     */
    public function createOrder(User $user, array $request): Order
    {
        return $this->orderRepository->createOrder($user, $request);
    }

    /**
     * Update the status of a specific order.
     *
     * @param  int  $orderId
     * @param  string  $status
     *
     * @return \App\Models\Order
     */
    public function updateOrderStatus(int $orderId, string $status): Order
    {
        return $this->orderRepository->updateOrderStatus($orderId, $status);
    }

    /**
     * Get a specific order.
     *
     * @param  int  $id
     *
     * @return \App\Models\Order
     */
    public function show(int $id)
    {
        return $this->orderRepository->show($id);
    }
}
