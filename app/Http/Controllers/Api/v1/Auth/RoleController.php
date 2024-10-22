<?php

namespace App\Http\Controllers\Api\v1\Auth;


use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Http\Requests\Auth\RoleStoreRequest;
use App\Http\Requests\Auth\RoleUpdateRequest;
use App\Http\Resources\RoleResource;
use App\Services\Auth\RoleService;
use App\Http\Requests\Auth\RoleIndexRequest;

class RoleController extends Controller
{
    public function __construct(protected RoleService $roleService) {}
    public function index(RoleIndexRequest $request)
    {
        return $this->roleService->index($request->all());
    }

    public function store(RoleStoreRequest $request)
    {
        $role = Role::create(['name' => $request->name, 'guard_name' => $request->guard_name ?? 'api']);
        return new RoleResource($role);
    }

    public function show($id)
    {
        return new RoleResource(Role::findOrFail($id));
    }

    public function update(RoleUpdateRequest $request, $id)
    {
        $role = Role::findOrFail($id);
        $role->update($request->validated());
        return response()->json($role);
    }

    public function destroy($id)
    {
        Role::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
