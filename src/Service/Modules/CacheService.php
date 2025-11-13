<?php

namespace App\Service\Modules;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Redis;

class CacheService
{
    public function __construct(
        private readonly CacheItemPoolInterface $cache,
        private readonly Redis $redis
    ) {}

    public function getOrSet(string $key, callable $callback, int $ttl = 3600): mixed
    {
        $cacheKey = $this->formatKey($key);
        $item = $this->cache->getItem($cacheKey);

        if (!$item->isHit()) {
            $value = $callback();
            $item->set($value);
            $item->expiresAfter($ttl);
            $this->cache->save($item);
            return $value;
        }

        return $item->get();
    }

    public function get(string $key): mixed
    {
        $cacheKey = $this->formatKey($key);
        return $this->cache->hasItem($cacheKey)
            ? $this->cache->getItem($cacheKey)->get()
            : null;
    }

    // Set value directly
    public function set(string $key, mixed $value, int $ttl = 3600): void
    {
        $cacheKey = $this->formatKey($key);
        $item = $this->cache->getItem($cacheKey);
        $item->set($value);
        //$item->expiresAfter($ttl);
        $this->cache->save($item);
    }

    // Delete key
    public function delete(string $key): void
    {
        $cacheKey = $this->formatKey($key);
        $this->cache->deleteItem($cacheKey);
    }

    public function deleteByPattern(string $pattern): void
    {
        $prefix = $_ENV['APP_PREFIX'] ?? 'app';
        $globPattern = $prefix . '_' . '*' . $pattern . '*';

        $iterator = null;
        do {
            $keys = $this->redis->scan($iterator, $globPattern);
            if ($keys !== false) {
                foreach ($keys as $key) {
                    $this->redis->del($key);
                }
            }
        } while ($iterator > 0);
    }

    // Format key (adds app prefix, for namespacing)
    private function formatKey(string $key): string
    {
        $prefix = $_ENV['APP_PREFIX'] ?? 'app';
        return "{$prefix}_{$key}";
    }
}
