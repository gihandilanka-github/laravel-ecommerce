<?php

namespace App\Repositories\Payment;

use App\Models\Payment;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PaymentRepository extends BaseRepository
{
    public function __construct(protected Payment $payment) {}

    public function index(array $request): Collection|LengthAwarePaginator
    {
        $payments = $this->payment->query();

        if (request()->filled('name')) {
            $payments->where('transaction_id', 'like', '%' . $request['name'] . '%');
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

    public function create(array $request)
    {
        return $this->payment->create($request);
    }

    public function show(int $id): payment
    {
        return $this->payment->find($id);
    }
}
