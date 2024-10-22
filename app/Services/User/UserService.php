<?php

namespace App\Services\User;

use App\Repositories\User\UserRepository;

class UserService
{
    public function __construct(protected UserRepository $userRepository) {}

    public function index(array $request)
    {
        return $this->userRepository->index($request);
    }

    public function show(int $id)
    {
        return $this->userRepository->show($id);
    }
}
