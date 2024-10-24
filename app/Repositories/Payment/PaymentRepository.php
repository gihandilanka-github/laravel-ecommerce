<?php

namespace App\Repositories\Payment;

use App\Models\Payment;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PaymentRepository extends BaseRepository
{
    public function __construct(protected Payment $payment) {}

    /**
     * Retrieve a list of payments with optional filters and sorting.
     *
     * @param array $request
     * @return Collection|LengthAwarePaginator
     */
    public function index(array $request): Collection|LengthAwarePaginator
    {
        $payments = $this->payment->query();

        if (request()->filled('transaction_id')) {
            $payments->where('transaction_id', 'like', '%' . $request['transaction_id'] . '%');
        }

        if (request()->filled('sortOrder')) {
            $payments->orderBy('created_at', $request['sortOrder']);
        }

        if (!request()->filled('sortOrder')) {
            $payments->orderBy('created_at', 'desc');
        }

        if (empty($request['limit'])) {
            return $payments->get();
        }

        return $payments->paginate($request['limit']);
    }

    /**
     * Create a new payment record.
     *
     * @param array $request
     * @return \App\Models\Payment
     */
    public function create(array $request): Payment
    {
        return $this->payment->create($request);
    }

    /**
     * Retrieve a single payment.
     *
     * @param int $id
     * @return \App\Models\Payment
     */
    public function show(int $id): Payment
    {
        return $this->payment->find($id);
    }
}
