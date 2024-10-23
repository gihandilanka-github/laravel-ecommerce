<?php

namespace App\Exceptions;

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
}
