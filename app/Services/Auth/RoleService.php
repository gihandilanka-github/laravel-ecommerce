<?php

namespace App\Services\Auth;

use App\Repositories\Auth\RoleRepository;

class RoleService
{
    public function __construct(protected RoleRepository $roleRepository) {}

    public function index(array $request)
    {
        return $this->roleRepository->index($request);
    }

    public function show(int $id)
    {
        return $this->roleRepository->show($id);
    }
}
