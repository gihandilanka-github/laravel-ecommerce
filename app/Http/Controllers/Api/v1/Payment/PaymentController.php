<?php

namespace App\Http\Controllers\Api\v1\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\PaymentIndexRequest;
use App\Http\Requests\Product\ProductIndexRequest;
use App\Http\Resources\Payment\PaymentCollection;
use App\Http\Resources\Payment\PaymentResource;
use App\Services\Payment\PaymentService;

class PaymentController extends Controller
{
    public function __construct(protected PaymentService $paymentService) {}

    /**
     * Retrieve a list of payments.
     *
     * @param  PaymentIndexRequest  $request
     * @return PaymentCollection
     */
    public function index(PaymentIndexRequest $request): PaymentCollection
    {
        return new PaymentCollection($this->paymentService->index($request->validated()));
    }

    /**
     * Retrieve a single payment.
     *
     * @param  int  $id
     * @return PaymentResource
     */
    public function show(int $id): PaymentResource
    {
        return new PaymentResource($this->paymentService->show($id));
    }
}
