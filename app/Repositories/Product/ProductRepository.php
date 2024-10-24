<?php

namespace App\Repositories\Product;

use App\Repositories\BaseRepository;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository extends BaseRepository
{
    public function __construct(protected Product $product) {}

    /**
     * Retrieve a list of products with optional filters and sorting.
     *
     * @param array $request
     * @return Collection|LengthAwarePaginator
     */
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

    /**
     * Generate a unique slug, given a string and an optional ID.
     *
     * Will check if the slug already exists in the database, and if so, will append a
     * counter to the slug to make it unique. If the ID is provided, it will be excluded
     * from the query.
     *
     * @param string $slug
     * @param int|null $id
     * @return string
     */
    public function ensureUniqueSlug(string $slug, $id = null): string
    {
        $originalSlug = $slug;
        $counter = 1;

        while (Product::where('slug', $slug)->when($id, function ($query, $id) {
            return $query->where('id', '!=', $id);
        })->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Create a new product.
     *
     * @param array $request
     * @return \App\Models\Product
     */
    public function create(array $request): Product
    {
        return $this->product->create($request);
    }

    /**
     * Retrieve a product by its ID.
     *
     * @param int $id
     * @return \App\Models\Product
     */
    public function show(int $id): Product
    {
        return $this->product->find($id);
    }
}
