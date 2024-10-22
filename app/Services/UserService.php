<?php

namespace App\Services;

use App\Repositories\UserRepository;

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
