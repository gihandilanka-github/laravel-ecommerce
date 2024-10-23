<?php

namespace App\Exceptions;

class InsufficientStockException extends OrderException
{
    protected string $errorCode = 'INSUFFICIENT_STOCK';
    protected int $statusCode = 422;

    public function __construct(
        string $productName,
        int $requestedQuantity,
        int $availableQuantity
    ) {
        $this->details = [
            'product_name' => $productName,
            'requested_quantity' => $requestedQuantity,
            'available_quantity' => $availableQuantity,
        ];

        parent::__construct(
            "Insufficient stock for product: {$productName}. " .
                "Requested: {$requestedQuantity}, Available: {$availableQuantity}"
        );
    }
}
