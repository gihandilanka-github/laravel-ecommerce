<?php

namespace App\Repositories\User;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\BaseRepository;

class UserRepository extends BaseRepository
{
    public function __construct(protected User $user) {}

    /**
     * Retrieve a list of users with optional filters and sorting.
     *
     * @param array $request
     * @return Collection|LengthAwarePaginator
     */
    public function index(array $request): Collection|LengthAwarePaginator
    {
        $users = $this->user->query();

        if (request()->filled('name')) {
            $users->where('name', 'like', '%' . $request['name'] . '%');
        }

        if (request()->filled('email')) {
            $users->where('email', 'like', '%' . $request['email'] . '%');
        }

        if (request()->filled('sortOrder')) {
            $users->orderBy('created_at', $request['sortOrder']);
        }

        if (!request()->filled('sortOrder')) {
            $users->orderBy('created_at', 'desc');
        }

        if (empty($request['limit'])) {
            return $users->get();
        }

        return $users->paginate($request['limit']);
    }

    /**
     * Retrieve a specific user.
     *
     * @param int $id
     * 
     * @return \App\Models\User
     */
    public function show(int $id): User
    {
        return $this->user->find($id);
    }
}
