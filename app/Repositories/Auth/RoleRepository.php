<?php

namespace App\Repositories\Auth;

use App\Models\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\BaseRepository;

class RoleRepository extends BaseRepository
{
    public function __construct(protected Role $role) {}

    public function index(array $request): Collection|LengthAwarePaginator
    {
        $roles = $this->role->query();

        if (request()->filled('name')) {
            $roles->where('name', 'like', '%' . $request['name'] . '%');
        }

        if (request()->filled('sortOrder')) {
            $roles->orderBy('created_at', $request['sortOrder']);
        }

        if (!request()->filled('sortOrder')) {
            $roles->orderBy('created_at', 'desc');
        }

        if (empty($request['limit'])) {
            return $roles->get();
        }

        return $roles->paginate($request['limit']);
    }

    public function show(int $id): Role
    {
        return $this->role->find($id);
    }
}
