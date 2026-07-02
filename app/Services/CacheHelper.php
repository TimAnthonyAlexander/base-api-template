<?php

namespace App\Services;

use BaseApi\Cache\Cache;

/**
 * CacheHelper - Enhanced caching utilities
 * 
 * Provides helpers for:
 * - TTL jitter to prevent cache stampede
 * - Namespaced cache keys
 * - Remember/forget patterns
 */
class CacheHelper
{
    /**
     * Generate cache key with namespace prefix
     * 
     * @param string $namespace Namespace (e.g., 'workspace', 'task', 'user')
     * @param string $key Unique identifier
     * @param array<string, mixed> $params Optional parameters for composite keys
     * @return string Namespaced cache key
     */
    public static function key(string $namespace, string $key, array $params = []): string
    {
        if ($params === []) {
            return sprintf('%s:%s', $namespace, $key);
        }

        $paramString = self::serializeParams($params);
        return sprintf('%s:%s:%s', $namespace, $key, $paramString);
    }

    /**
     * Add jitter to TTL to prevent cache stampede
     * 
     * When many cache entries expire at the same time, all requests
     * regenerate the cache simultaneously (thundering herd problem).
     * Jitter spreads expiration times over a window.
     * 
     * @param int $ttl Base TTL in seconds
     * @param float $jitterPercent Jitter as percentage (0.0-1.0, default 0.1 = 10%)
     * @return int TTL with random jitter added
     */
    public static function ttlWithJitter(int $ttl, float $jitterPercent = 0.1): int
    {
        $jitter = (int) ($ttl * $jitterPercent);
        return $ttl + random_int(-$jitter, $jitter);
    }

    /**
     * Remember pattern: get from cache or execute callback
     * 
     * @param string $namespace Cache namespace
     * @param string $key Cache key
     * @param int $ttl TTL in seconds
     * @param callable $callback Callback to generate value if cache miss
     * @param bool $useJitter Whether to add TTL jitter (default: true)
     * @return mixed Cached or freshly generated value
     */
    public static function remember(string $namespace, string $key, int $ttl, callable $callback, bool $useJitter = true): mixed
    {
        $cacheKey = self::key($namespace, $key);
        $cached = Cache::get($cacheKey);

        if ($cached !== null) {
            return $cached;
        }

        $value = $callback();
        $actualTtl = $useJitter ? self::ttlWithJitter($ttl) : $ttl;
        Cache::put($cacheKey, $value, $actualTtl);

        return $value;
    }

    /**
     * Forget cached value
     *
     * @param string $namespace Cache namespace
     * @param string $key Cache key
     */
    public static function forget(string $namespace, string $key): void
    {
        $cacheKey = self::key($namespace, $key);
        Cache::forget($cacheKey);
    }

    /**
     * Serialize parameters for composite cache keys
     * 
     * @param array<string, mixed> $params Parameters
     * @return string Serialized parameter string
     */
    private static function serializeParams(array $params): string
    {
        ksort($params);
        $parts = [];
        foreach ($params as $k => $v) {
            $parts[] = $k . '=' . (is_array($v) ? md5(json_encode($v) ?: '') : $v);
        }

        return implode('&', $parts);
    }

    /**
     * Get many keys at once (for batch operations)
     * 
     * @param string $namespace Cache namespace
     * @param array<string> $keys Array of cache keys
     * @return array<string, mixed> Map of key => value (missing keys not included)
     */
    public static function getMany(string $namespace, array $keys): array
    {
        $result = [];
        foreach ($keys as $key) {
            $cacheKey = self::key($namespace, $key);
            $value = Cache::get($cacheKey);
            if ($value !== null) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Put many keys at once
     *
     * @param string $namespace Cache namespace
     * @param array<string, mixed> $values Map of key => value
     * @param int $ttl TTL in seconds
     * @param bool $useJitter Whether to add TTL jitter
     */
    public static function putMany(string $namespace, array $values, int $ttl, bool $useJitter = true): void
    {
        $actualTtl = $useJitter ? self::ttlWithJitter($ttl) : $ttl;
        foreach ($values as $key => $value) {
            $cacheKey = self::key($namespace, $key);
            Cache::put($cacheKey, $value, $actualTtl);
        }
    }
}
