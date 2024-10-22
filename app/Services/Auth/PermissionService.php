<?php

namespace App\Services\Auth;

use App\Repositories\PermissionRepository;

class PermissionService
{
    public function __construct(protected PermissionRepository  $permissionRepository) {}

    public function index(array $request)
    {
        return $this->permissionRepository->index($request);
    }

    public function show(int $id)
    {
        return $this->permissionRepository->show($id);
    }
}
