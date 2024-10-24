<?php

namespace App\Services\User;

use App\Repositories\User\UserRepository;
use Illuminate\Support\Arr;

class UserService
{
    public function __construct(protected UserRepository $userRepository) {}

    public function index(array $request)
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

    public function show(int $id)
    {
        return $this->userRepository->show($id);
    }
}
