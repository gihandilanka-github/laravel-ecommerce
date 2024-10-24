<?php

namespace App\Services\User;

use App\Models\User;
use App\Repositories\User\UserRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class UserService
{
    public function __construct(protected UserRepository $userRepository) {}

    /**
     * Retrieve a list of users with optional filters and sorting.
     *
     * @param  array  $request
     * @return Collection|LengthAwarePaginator
     */
    public function index(array $request): Collection|LengthAwarePaginator
    {
        $userCacheListTag = config('constants.user.default_cache_tag_prefix');

        if (!empty($request['limit'])) {
            $userCacheListTag = $userCacheListTag . 'UserListPaginated';
        }

        $cacheKey = generateCacheKey(Arr::only($request, ['name']));
        $cacheData = getCache($userCacheListTag, $cacheKey);

        if ($cacheData) {
            logger()->info('UserList: get data from cache', [$userCacheListTag, $cacheKey]);
            return $cacheData;
        }

        logger()->info('UserList: get data from database');
        $users = $this->userRepository->index($request);
        putCache($userCacheListTag, $cacheKey, $users, config('constants.user.default_cache_time'));
    }

    /**
     * Retrieve a specific user.
     *
     * @param  int  $id
     * @return \App\Models\User
     */
    public function show(int $id): User
    {
        return $this->userRepository->show($id);
    }
}
