<?php

namespace App\Exceptions\Order;

use Exception;

class OrderException extends Exception
{
    protected string $errorCode = 'ORDER_ERROR';
    protected array $details = [];
    protected int $statusCode = 400;

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getDetails(): array
    {
        return $this->details;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
