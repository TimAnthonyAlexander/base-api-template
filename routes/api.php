<?php

use BaseApi\App;
use App\Controllers\HealthController;
use App\Controllers\LoginController;
use App\Controllers\LogoutController;
use App\Controllers\MeController;
use App\Controllers\SignupController;
use App\Controllers\FileUploadController;
use BaseApi\Http\Middleware\RateLimitMiddleware;
use BaseApi\Http\Middleware\AuthMiddleware;
use BaseApi\Http\Middleware\CsrfMiddleware;

$router = App::router();

// Health check endpoints
$router->get(
    '/health',
    [
        RateLimitMiddleware::class => ['limit' => '60/1m'],
        HealthController::class,
    ],
);

$router->post(
    '/health',
    [
        HealthController::class,
    ],
);

// Authentication endpoints
$router->post(
    '/auth/signup',
    [
        SignupController::class,
    ],
);

$router->post(
    '/auth/login',
    [
        LoginController::class,
    ],
);

$router->post(
    '/auth/logout',
    [
        AuthMiddleware::class,
        LogoutController::class,
    ],
);

// Protected endpoints
$router->get(
    '/me',
    [
        AuthMiddleware::class,
        CsrfMiddleware::class,
        MeController::class,
    ],
);

// File upload endpoints
$router->post(
    '/files/upload',
    [
        RateLimitMiddleware::class => ['limit' => '10/1m'],
        FileUploadController::class,
    ],
);

$router->post(
    '/files/upload-public',
    [
        RateLimitMiddleware::class => ['limit' => '10/1m'],
        [FileUploadController::class, 'uploadPublic'],
    ],
);

$router->post(
    '/files/upload-custom',
    [
        RateLimitMiddleware::class => ['limit' => '10/1m'],
        [FileUploadController::class, 'uploadWithCustomName'],
    ],
);

$router->get(
    '/files/info',
    [
        [FileUploadController::class, 'getFileInfo'],
    ],
);

$router->delete(
    '/files',
    [
        [FileUploadController::class, 'deleteFile'],
    ],
);
