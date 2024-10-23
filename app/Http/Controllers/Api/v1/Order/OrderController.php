<?php

namespace App\Http\Controllers\Api\v1\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderCreateRequest;
use App\Http\Requests\Order\OrderIndexRequest;
use App\Http\Resources\Order\OrderResource;
use App\Services\Order\OrderService;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    public function index(OrderIndexRequest $request)
    {
        return $this->orderService->index($request->validated());
    }

    public function store(OrderCreateRequest $request)
    {
        return new OrderResource($this->orderService->store($request->validated()));
    }
}
