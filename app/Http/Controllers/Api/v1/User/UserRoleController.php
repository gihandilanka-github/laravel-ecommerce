<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleAssignRequest;
use App\Http\Requests\RoleRevokeRequest;
use App\Models\User;

class UserRoleController extends Controller
{
    public function assignRole(RoleAssignRequest $request, User $user)
    {

        $user->assignRole($request->role);
        return response()->json(['message' => 'Role assigned successfully']);
    }

    public function revokeRole(RoleRevokeRequest $request, User $user)
    {
        $user->removeRole($request->role);
        return response()->json(['message' => 'Role revoked successfully']);
    }
}
