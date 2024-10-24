<?php

namespace App\Http\Controllers\Api\v1\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductIndexRequest;
use App\Http\Resources\Payment\PaymentCollection;
use App\Http\Resources\Payment\PaymentResource;
use App\Services\Payment\PaymentService;

class ProductController extends Controller
{
    public function __construct(protected PaymentService $paymentService) {}

    public function index(ProductIndexRequest $request)
    {
        return new PaymentCollection($this->paymentService->index($request->validated()));
    }

    public function show(int $id)
    {
        return new PaymentResource($this->paymentService->show($id));
    }
}
