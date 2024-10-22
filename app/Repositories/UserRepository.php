<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class UserRepository extends BaseRepository
{
    public function __construct(protected User $user) {}

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

    public function show(int $id): User
    {
        return $this->user->find($id);
    }
}
