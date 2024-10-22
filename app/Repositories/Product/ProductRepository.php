<?php

namespace App\Repositories\Product;

use App\Repositories\BaseRepository;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository extends BaseRepository
{
    public function __construct(protected Product $product) {}

    public function index(array $request): Collection|LengthAwarePaginator
    {
        $products = $this->product->query();

        if (request()->filled('name')) {
            $products->where('name', 'like', '%' . $request['name'] . '%');
        }

        if (request()->filled('sortOrder')) {
            $products->orderBy('created_at', $request['sortOrder']);
        }

        if (!request()->filled('sortOrder')) {
            $products->orderBy('created_at', 'desc');
        }

        if (empty($request['limit'])) {
            return $products->get();
        }

        return $products->paginate($request['limit']);
    }

    public function create(array $request)
    {
        return $this->product->create($request);
    }
}
