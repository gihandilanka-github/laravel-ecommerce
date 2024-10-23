<?php

namespace App\Services\Order;

use App\Repositories\Order\OrderRepository;

class OrderService
{
    public function __construct(protected OrderRepository  $orderRepository) {}

    public function index(array $request)
    {
        return $this->orderRepository->index($request);
    }

    public function store(array $request)
    {
        return $this->orderRepository->create($request);
    }
}
