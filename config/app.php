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
];
