<?php

namespace App\Http\Controllers\Api\v1\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderCreateRequest;
use App\Http\Requests\Order\OrderIndexRequest;
use App\Http\Requests\Order\OrderUpdateStatusRequest;
use App\Http\Resources\Order\OrderCollection;
use App\Http\Resources\Order\OrderResource;
use App\Services\Order\OrderService;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService) {}


    /**
     * Retrieve a list of orders.
     *
     * @param  OrderIndexRequest  $request
     * @return OrderCollection
     */
    public function index(OrderIndexRequest $request): OrderCollection
    {
        return new OrderCollection($this->orderService->index($request->validated()));
    }

    /**
     * Retrieve a single order.
     *
     * @param  int  $id
     * @return OrderResource
     */
    public function show(int $id): OrderResource
    {
        return new OrderResource($this->orderService->show($id));
    }

    /**
     * Create a new order.
     *
     * @param  OrderCreateRequest  $request
     * @return OrderResource
     */
    public function store(OrderCreateRequest $request): OrderResource
    {
        return new OrderResource($this->orderService->createOrder($request->user(), $request->validated()));
    }

    /**
     * Update the status of a specific order.
     *
     * @param  OrderUpdateStatusRequest  $request
     * @param  int  $orderId
     * @return OrderResource
     */
    public function updateOrderStatus(OrderUpdateStatusRequest $request, $orderId): OrderResource
    {
        return new OrderResource($this->orderService->updateOrderStatus($orderId, $request->validated()['status']));
    }
}
