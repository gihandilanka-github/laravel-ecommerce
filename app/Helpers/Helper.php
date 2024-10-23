<?php

use Illuminate\Support\Facades\Cache;

if (!function_exists('getCache')) {
    function getCache($cacheTag, $cacheKey)
    {
        if (config('cache.default') === 'file') {
            return cache()->get(generateCacheKey($cacheKey));
        }
        if (config('cache.default') != 'redis') {
            return false;
        }
        return Cache::tags($cacheTag)->get(generateCacheKey($cacheKey));
    }
}

if (!function_exists('putCache')) {
    function putCache($cacheTag, $cacheKey, $cacheData, $time = null)
    {
        if (!isset($time)) {
            $time = config('constants.general.CACHE_EXPIRY');
        }

        if (config('cache.default') === 'file') {
            return cache([generateCacheKey($cacheKey) => $cacheData], $time);
        }

        if (config('cache.default') != 'redis') {
            return false;
        }

        return Cache::tags($cacheTag)->put(generateCacheKey($cacheKey), $cacheData, $time);
    }
}

if (!function_exists('rememberCache')) {
    function rememberCache($cacheTag, $cacheKey, \Closure $closureData, $time = null)
    {
        if (!isset($time)) {
            $time = config('constants.general.CACHE_EXPIRY');
        }

        if (config('cache.default') === 'file') {
            return Cache::remember(generateCacheKey($cacheKey), $time, $closureData);
        }

        if (config('cache.default') != 'redis') {
            return $closureData;
        }

        return Cache::tags($cacheTag)->remember(generateCacheKey($cacheKey), $time, $closureData);
    }
}

if (!function_exists('clearCache')) {
    function clearCache($cacheTags)
    {
        if (config('cache.default') === 'file') {
            cache()->flush();
            return true;
        }

        if (config('cache.default') != 'redis') {
            return false;
        }
        return Cache::tags($cacheTags)->flush();
    }
}

if (!function_exists('generateCacheKey')) {
    function generateCacheKey($cacheKeys)
    {
        $result = '';
        if (is_array($cacheKeys)) {
            foreach ($cacheKeys as $key => $value) {
                if ($result) {
                    $result .= '#';
                }
                $result .= "$key:$value";
            }
        } else {
            $result = $cacheKeys;
        }
        return $result;
    }
}

if (!function_exists('generateCacheHash')) {
    function generateCacheHash($cacheKeys)
    {
        $result = '';
        if (is_array($cacheKeys)) {
            $result = md5(json_encode($cacheKeys));
        } else {
            $result = md5($cacheKeys);
        }
        return $result;
    }
}
