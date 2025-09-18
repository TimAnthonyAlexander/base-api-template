<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by your application. A "local" driver is available out of the box.
    |
    */
    
    'default' => $_ENV['FILESYSTEM_DISK'] ?? 'local',

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Configure your application's filesystem disks here. The "local" disk
    | is configured for you out of the box and uses the local filesystem.
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'url' => ($_ENV['APP_URL'] ?? 'http://localhost:8000') . '/storage',
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => ($_ENV['APP_URL'] ?? 'http://localhost:8000') . '/storage',
            'visibility' => 'public',
        ],

    ],

];
