<?php

namespace App\Exceptions\Order;

use Illuminate\Http\JsonResponse;

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

        logger()->error("Insufficient stock for product: {$productName}. Requested: {$requestedQuantity}, Available: {$availableQuantity}");

        parent::__construct(
            "Insufficient stock for product: {$productName}. " .
                "Requested: {$requestedQuantity}, Available: {$availableQuantity}"
        );
    }

    public function render($request): JsonResponse
    {
        return response()->json([
            'data' => null,
            'error' => $this->errorCode,
            'message' => "Insufficient stock for product: {$this->details['product_name']}. Requested: {$this->details['requested_quantity']}, Available: {$this->details['available_quantity']}.",
        ], 400); // 400 Bad Request
    }
}
