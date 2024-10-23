<?php

namespace App\Exceptions;

class PaymentFailedException extends OrderException
{
    protected string $errorCode = 'PAYMENT_FAILED';
    protected int $statusCode = 422;

    public function __construct(
        string $reason,
        ?string $transactionId = null
    ) {
        $this->details = [
            'reason' => $reason,
            'transaction_id' => $transactionId,
        ];

        parent::__construct("Payment failed: {$reason}");
    }
}
