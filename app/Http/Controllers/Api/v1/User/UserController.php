<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Services\User\UserService;
use App\Http\Requests\User\UserIndexRequest;
use App\Http\Resources\User\UserCollection;
use App\Http\Resources\User\UserResource;

class UserController extends Controller
{

    public function __construct(protected UserService $userService) {}

    /**
     * Retrieve a list of users.
     *
     * @param  UserIndexRequest  $request
     * @return UserCollection
     */
    public function index(UserIndexRequest $request): UserCollection
    {
        return new UserCollection($this->userService->index($request->all()));
    }

    /**
     * Retrieve a single user.
     *
     * @param  int  $id
     * @return UserResource
     */
    public function show(int $id): UserResource
    {
        return new UserResource($this->userService->show($id));
    }
}
