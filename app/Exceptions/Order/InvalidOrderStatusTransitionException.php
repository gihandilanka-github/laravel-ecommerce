<?php

namespace App\Exceptions\Order;

use Illuminate\Http\JsonResponse;

class InvalidOrderStatusTransitionException extends OrderException
{
    protected string $errorCode = 'INVALID_STATUS_TRANSITION';
    protected int $statusCode = 422;

    public function __construct(
        string $currentStatus,
        string $newStatus
    ) {
        $this->details = [
            'current_status' => $currentStatus,
            'requested_status' => $newStatus,
        ];

        parent::__construct(
            "Invalid order status transition from {$currentStatus} to {$newStatus}"
        );
    }

    public function render($request): JsonResponse
    {
        return response()->json([
            'data' => null,
            'error' => $this->errorCode,
            'message' => "Invalid order status transition from {$this->details['current_status']} to {$this->details['requested_status']}",
        ], 400);
    }
}
