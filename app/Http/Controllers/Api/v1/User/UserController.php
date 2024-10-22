<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Services\User\UserService;
use App\Http\Requests\User\UserIndexRequest;

class UserController extends Controller
{

    public function __construct(protected UserService $userService) {}

    public function index(UserIndexRequest $request)
    {
        return $this->userService->index($request->all());
    }

    public function show(int $id)
    {
        return $this->userService->show($id);
    }
}
