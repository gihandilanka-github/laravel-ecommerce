<?php

namespace App\Http\Controllers\Api\v1\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderCreateRequest;
use App\Http\Requests\Order\OrderIndexRequest;
use App\Http\Requests\Order\OrderUpdateStatusRequest;
use App\Http\Resources\Order\OrderResource;
use App\Services\Order\OrderService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    public function index(OrderIndexRequest $request)
    {
        return $this->orderService->index($request->validated());
    }

    public function store(OrderCreateRequest $request)
    {
        return new OrderResource($this->orderService->createOrder($request->user(), $request->validated()));
    }

    public function updateOrderStatus(OrderUpdateStatusRequest $request, $orderId)
    {
        return new OrderResource($this->orderService->updateOrderStatus($orderId, $request->validated()['status']));
    }
}
