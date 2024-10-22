<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Http\Requests\UserIndexRequest;

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
