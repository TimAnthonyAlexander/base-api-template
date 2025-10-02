<?php

/**
 * BaseAPI Application Configuration
 * 
 * This file contains the main application configuration that overrides the 
 * framework defaults found in baseapi/config/defaults.php. The Config class
 * (baseapi/src/Config.php) handles environment variable resolution and 
 * dot-notation access to these settings.
 * 
 * Environment variables take precedence over these defaults, allowing for
 * flexible deployment-specific configuration without code changes.
 */
return [
    /*
    |--------------------------------------------------------------------------
    | Application Settings
    |--------------------------------------------------------------------------
    |
    | Core application configuration including environment detection, debug
    | mode, and server binding settings. These override the framework defaults
    | of: env='local', debug=true, url='http://127.0.0.1:7879', 
    | host='127.0.0.1', port=7879.
    |
    */
    'app' => [
        // Application environment: local, staging, production
        // Used for environment-specific behavior and error handling
        'env' => $_ENV['APP_ENV'] ?? 'local',

        // Enable debug mode for detailed error messages and stack traces
        // Should be false in production for security and performance
        'debug' => $_ENV['APP_DEBUG'] ?? true,

        // Full application URL used for asset generation and redirects
        // Must include protocol (http/https) and port if non-standard
        'url' => $_ENV['APP_URL'] ?? 'http://127.0.0.1:7879',

        // Server host to bind to (0.0.0.0 for all interfaces, 127.0.0.1 for localhost only)
        'host' => $_ENV['APP_HOST'] ?? '127.0.0.1',

        // Port number for the web server to listen on
        'port' => $_ENV['APP_PORT'] ?? 7879,

        'response_time' => $_ENV['APP_RESPONSE_TIME'] ?? false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Service Providers
    |--------------------------------------------------------------------------
    |
    | Service providers are registered during application bootstrap to bind
    | services into the dependency injection container and configure framework
    | features. Add your custom providers here.
    |
    */
    'providers' => [
        \App\Providers\AppServiceProvider::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS)
    |--------------------------------------------------------------------------
    |
    | CORS configuration controls which origins are allowed to make cross-origin
    | requests to your API. The default allowlist includes common development
    | frontend servers (Vite dev server on ports 5173).
    |
    */
    'cors' => [
        // Comma-separated list of allowed origins for CORS requests
        // Framework default: ['http://127.0.0.1:5173', 'http://localhost:5173']
        'allowlist' => explode(',', $_ENV['CORS_ALLOWLIST'] ?? 'http://127.0.0.1:5173,http://localhost:5173'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Configuration
    |--------------------------------------------------------------------------
    |
    | Database connection settings. Supports MySQL and SQLite out of the box.
    | Framework defaults: host='127.0.0.1', port=7878, name='baseapi',
    | user='root', password='', charset='utf8mb4', persistent=false.
    | 
    | For SQLite, set DB_DRIVER=sqlite and DB_NAME to file path or ':memory:'.
    | See baseapi/config/database.php for more examples.
    |
    */
    'database' => [
        // Database server hostname or IP address
        'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',

        // Database server port (3306 for MySQL, 7878 is custom default)
        'port' => $_ENV['DB_PORT'] ?? 7878,

        // Database name or SQLite file path
        'name' => $_ENV['DB_NAME'] ?? 'baseapi',

        // Database username for authentication
        'user' => $_ENV['DB_USER'] ?? 'root',

        // Database password for authentication
        'password' => $_ENV['DB_PASSWORD'] ?? '',

        // Character set for database connections (utf8mb4 supports full Unicode)
        'charset' => 'utf8mb4',

        // Whether to use persistent database connections (can improve performance)
        'persistent' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | BaseAPI includes a unified caching layer supporting multiple drivers.
    | This configuration defines cache stores, TTL settings, and specialized
    | caching for queries and HTTP responses. See baseapi/config/cache.php
    | for detailed documentation of each option.
    |
    */
    'cache' => [
        // Default cache store to use when none specified
        // Options: 'array' (memory), 'file' (filesystem), 'redis'
        'default' => $_ENV['CACHE_DRIVER'] ?? 'file',

        /*
        | Cache Store Definitions
        |
        | Each store defines a caching backend with its specific configuration.
        | Multiple stores can use the same driver with different settings.
        */
        'stores' => [
            // In-memory cache store (fastest, but data lost between requests)
            // Best for single-request caching and testing
            'array' => [
                'driver' => 'array',
                'serialize' => false, // No need to serialize for memory storage
            ],

            // File-based cache store (persistent across requests)
            // Good balance of performance and simplicity
            'file' => [
                'driver' => 'file',
                'path' => $_ENV['CACHE_PATH'] ?? null, // Uses storage/cache by default when null
                'permissions' => 0755, // File permissions for cache files
            ],

            // Redis cache store (fastest persistent cache, supports advanced features)
            // Requires Redis server, best for production and distributed systems
            'redis' => [
                'driver' => 'redis',
                'host' => $_ENV['REDIS_HOST'] ?? '127.0.0.1',
                'password' => $_ENV['REDIS_PASSWORD'] ?? null,
                'port' => $_ENV['REDIS_PORT'] ?? 6379,
                'database' => $_ENV['REDIS_CACHE_DB'] ?? 1, // Redis database number (0-15)
                'timeout' => 5.0,        // Connection timeout in seconds
                'retry_interval' => 100, // Retry interval in milliseconds
                'read_timeout' => 60.0,  // Read timeout in seconds
            ],
        ],

        // Cache key prefix to avoid collisions with other applications
        // All cache keys are prefixed with this string
        'prefix' => $_ENV['CACHE_PREFIX'] ?? 'baseapi_cache',

        // Default TTL (time-to-live) in seconds when none specified
        // Framework default: 3600 seconds (1 hour)
        'default_ttl' => (int)($_ENV['CACHE_DEFAULT_TTL'] ?? 3600),

        // Whether to serialize complex data types before storage
        // Required for file and Redis stores to handle objects/arrays
        'serialize' => true,

        // Note: Model query caching is planned but not yet implemented

        /*
        | HTTP Response Cache
        |
        | Middleware-based caching of HTTP responses to reduce server load.
        | Particularly useful for API endpoints with relatively static data.
        */
        'response_cache' => [
            // Enable HTTP response caching middleware
            // Framework default: false (disabled by default)
            'enabled' => $_ENV['CACHE_RESPONSES'] ?? false,

            // Default TTL for cached responses (10 minutes)
            'default_ttl' => (int)($_ENV['CACHE_RESPONSE_TTL'] ?? 600),

            // Cache key prefix for response cache
            'prefix' => 'response',

            // HTTP headers that affect cache key generation
            // Responses vary based on these headers (content negotiation, auth)
            'vary_headers' => ['Accept', 'Accept-Encoding', 'Authorization'],

            // Query parameters to ignore when generating cache keys
            // Useful for cache-busting parameters and timestamps
            'ignore_query_params' => ['_t', 'timestamp', 'cache_bust'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Filesystem Configuration
    |--------------------------------------------------------------------------
    |
    | File storage configuration defining disk drivers and their settings.
    | Supports local filesystem storage with planned support for cloud storage
    | (S3, Google Cloud Storage). See baseapi/config/filesystems.php for
    | additional driver examples and configuration options.
    |
    */
    'filesystems' => [
        // Default disk to use when none specified
        // Framework default: 'local'
        'default' => $_ENV['FILESYSTEM_DISK'] ?? 'local',

        /*
        | Filesystem Disks
        |
        | Each disk represents a storage location with specific configuration.
        | You can define multiple disks using the same driver for different purposes.
        */
        'disks' => [
            // Local filesystem disk for general file storage
            // Files stored in storage/app directory
            'local' => [
                'driver' => 'local',
                'root' => 'storage/app', // Storage root directory
                'url' => ($_ENV['APP_URL'] ?? 'http://localhost:7879') . '/storage', // Public URL base
            ],

            // Public filesystem disk for publicly accessible files
            // Files stored in storage/app/public directory
            'public' => [
                'driver' => 'local',
                'root' => 'storage/app/public', // Public storage root
                'url' => ($_ENV['APP_URL'] ?? 'http://localhost:7879') . '/storage', // Public URL base
                'visibility' => 'public', // Files are publicly accessible
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    |
    | Background job queue configuration for asynchronous task processing.
    | Worker settings are configured via command-line options when running workers.
    | Only the driver needs to be configured here.
    |
    */
    'queue' => [
        // Default queue driver: 'sync' (development), 'database' (production)
        // Framework default: 'sync' (executes immediately)
        'default' => $_ENV['QUEUE_DRIVER'] ?? 'sync',
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Configure where and how application logs are written. BaseAPI supports
    | file-based logging (writes to storage/logs) and stderr logging (writes
    | to PHP's error_log). Log levels filter messages by severity.
    |
    */
    'logging' => [
        // Logging channel: 'file' writes to storage/logs, 'stderr' writes to error_log
        // Framework default: 'file'
        'default' => $_ENV['LOG_CHANNEL'] ?? 'file',

        // Log file path relative to application root
        // Framework default: 'storage/logs/baseapi.log'
        'path' => $_ENV['LOG_FILE'] ?? 'storage/logs/baseapi.log',

        // Minimum log level: debug, info, warn, error
        // Framework default: 'debug'
        'level' => $_ENV['LOG_LEVEL'] ?? 'debug',
    ],
];
