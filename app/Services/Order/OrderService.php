<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Repositories\Order\OrderRepository;
use App\Models\User;

class OrderService
{
    public function __construct(protected OrderRepository  $orderRepository) {}

    public function index(array $request)
    {
        return $this->orderRepository->index($request);
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
