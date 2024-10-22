<?php

namespace App\Repositories\Auth;

use App\Models\Permission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\BaseRepository;

class PermissionRepository extends BaseRepository
{
    public function __construct(protected Permission $permission) {}

    public function index(array $request): Collection|LengthAwarePaginator
    {
        $permissions = $this->permission->query();

        if (request()->filled('name')) {
            $permissions->where('name', 'like', '%' . $request['name'] . '%');
        }

        if (request()->filled('sortOrder')) {
            $permissions->orderBy('created_at', $request['sortOrder']);
        }

        if (!request()->filled('sortOrder')) {
            $permissions->orderBy('created_at', 'desc');
        }

        if (empty($request['limit'])) {
            return $permissions->get();
        }

        return $permissions->paginate($request['limit']);
    }
}
