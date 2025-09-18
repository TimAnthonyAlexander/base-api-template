<?php

// Application configuration
// Override framework defaults here

return [
    'app' => [
        'env' => $_ENV['APP_ENV'] ?? 'local',
        'debug' => $_ENV['APP_DEBUG'] ?? true,
        'url' => $_ENV['APP_URL'] ?? 'http://127.0.0.1:7879',
        'host' => $_ENV['APP_HOST'] ?? '127.0.0.1',
        'port' => $_ENV['APP_PORT'] ?? 7879,
    ],

    'providers' => [
        \App\Providers\AppServiceProvider::class,
    ],

    'cors' => [
        'allowlist' => explode(',', $_ENV['CORS_ALLOWLIST'] ?? 'http://127.0.0.1:5173,http://localhost:5173'),
    ],

    'database' => [
        'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
        'port' => $_ENV['DB_PORT'] ?? 7878,
        'name' => $_ENV['DB_NAME'] ?? 'baseapi',
        'user' => $_ENV['DB_USER'] ?? 'root',
        'password' => $_ENV['DB_PASSWORD'] ?? '',
        'charset' => 'utf8mb4',
        'persistent' => false,
    ],

    'cache' => [
        'default' => $_ENV['CACHE_DRIVER'] ?? 'file',
        'stores' => [
            'array' => [
                'driver' => 'array',
                'serialize' => false,
            ],
            'file' => [
                'driver' => 'file',
                'path' => $_ENV['CACHE_PATH'] ?? null, // Uses storage/cache by default
                'permissions' => 0755,
            ],
            'redis' => [
                'driver' => 'redis',
                'host' => $_ENV['REDIS_HOST'] ?? '127.0.0.1',
                'password' => $_ENV['REDIS_PASSWORD'] ?? null,
                'port' => $_ENV['REDIS_PORT'] ?? 6379,
                'database' => $_ENV['REDIS_CACHE_DB'] ?? 1,
                'timeout' => 5.0,
                'retry_interval' => 100,
                'read_timeout' => 60.0,
            ],
        ],
        'prefix' => $_ENV['CACHE_PREFIX'] ?? 'baseapi_cache',
        'default_ttl' => (int)($_ENV['CACHE_DEFAULT_TTL'] ?? 3600), // 1 hour
        'serialize' => true,
        'query_cache' => [
            'enabled' => $_ENV['CACHE_QUERIES'] ?? true,
            'default_ttl' => (int)($_ENV['CACHE_QUERY_TTL'] ?? 300), // 5 minutes
            'prefix' => 'query',
            'tag_prefix' => 'model',
        ],
        'response_cache' => [
            'enabled' => $_ENV['CACHE_RESPONSES'] ?? false,
            'default_ttl' => (int)($_ENV['CACHE_RESPONSE_TTL'] ?? 600), // 10 minutes
            'prefix' => 'response',
            'vary_headers' => ['Accept', 'Accept-Encoding', 'Authorization'],
            'ignore_query_params' => ['_t', 'timestamp', 'cache_bust'],
        ],
    ],

    'filesystems' => [
        'default' => $_ENV['FILESYSTEM_DISK'] ?? 'local',
        'disks' => [
            'local' => [
                'driver' => 'local',
                'root' => 'storage/app',
                'url' => ($_ENV['APP_URL'] ?? 'http://localhost:7879') . '/storage',
            ],
            'public' => [
                'driver' => 'local',
                'root' => 'storage/app/public',
                'url' => ($_ENV['APP_URL'] ?? 'http://localhost:7879') . '/storage',
                'visibility' => 'public',
            ],
        ],
    ],
];
