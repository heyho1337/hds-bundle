<?php

namespace App\Service\Modules;

use Symfony\Contracts\Cache\CacheInterface;

class CacheService
{
    public function __construct(
        private CacheInterface $cache
    )
    {

    }

    public function getFromCache(string $key,string $value, callable $callback)
    {
        $cacheKey = sprintf("{$_ENV['APP_PREFIX']}_{$key}%s", $value);
        return $this->cache->get($cacheKey, $callback);
    }
}