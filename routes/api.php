<?php

use BaseApi\App;
use App\Controllers\HealthController;
use App\Controllers\LoginController;
use App\Controllers\LogoutController;
use App\Controllers\MeController;
use App\Controllers\SignupController;
use App\Controllers\FileUploadController;
use App\Controllers\BenchmarkController;
use App\Controllers\OpenApiController;
use App\Controllers\ApiTokenController;
use App\Controllers\StreamController;
use BaseApi\Http\Middleware\RateLimitMiddleware;
use BaseApi\Permissions\PermissionsMiddleware;
use App\Middleware\CombinedAuthMiddleware;

$router = App::router();

// ================================
// Public Endpoints (No Auth)
// ================================

// Health check
$router->get('/health', [
    RateLimitMiddleware::class => ['limit' => '60/1m'],
    HealthController::class,
]);

// Benchmark endpoint (no middleware for performance testing)
$router->get('/benchmark', [BenchmarkController::class]);

// ================================  
// Authentication Endpoints
// ================================

// User registration
$router->post('/auth/signup', [
    RateLimitMiddleware::class => ['limit' => '5/1m'],
    SignupController::class,
]);

// User login
$router->post('/auth/login', [
    RateLimitMiddleware::class => ['limit' => '10/1m'],
    LoginController::class,
]);

// User logout (supports both session and API token auth)
$router->post('/auth/logout', [
    CombinedAuthMiddleware::class,
    LogoutController::class,
]);

// ================================
// Protected Endpoints (Combined Auth)
// ================================

// Get current user info (supports both session and API token)
$router->get('/me', [
    CombinedAuthMiddleware::class,
    MeController::class,
]);

// API token management (supports both session and API token)
$router->get('/api-tokens', [
    CombinedAuthMiddleware::class,
    ApiTokenController::class,
]);

$router->post('/api-tokens', [
    CombinedAuthMiddleware::class,
    RateLimitMiddleware::class => ['limit' => '10/1h'],
    ApiTokenController::class,
]);

$router->delete('/api-tokens/{id}', [
    CombinedAuthMiddleware::class,
    ApiTokenController::class,
]);

// ================================
// File Upload Examples
// ================================

// Basic file upload
$router->post('/files/upload', [
    CombinedAuthMiddleware::class,
    RateLimitMiddleware::class => ['limit' => '10/1m'],
    FileUploadController::class,
]);

// Get file info
$router->get('/files/info', [
    CombinedAuthMiddleware::class,
    FileUploadController::class,
]);

// Delete files (with permission check example)
$router->delete('/files', [
    CombinedAuthMiddleware::class,
    PermissionsMiddleware::class => ['node' => 'files.delete'],
    FileUploadController::class,
]);

// ================================
// Permission-Protected Examples
// ================================
// 
// Examples of using PermissionsMiddleware:
//
// $router->post('/admin/users', [
//     CombinedAuthMiddleware::class,
//     PermissionsMiddleware::class => ['node' => 'admin.users.create'],
//     AdminUsersController::class,
// ]);
//
// $router->get('/premium/content', [
//     CombinedAuthMiddleware::class,
//     PermissionsMiddleware::class => ['node' => 'content.premium'],
//     PremiumContentController::class,
// ]);
//
// Wildcard permission example:
// $router->post('/export/csv', [
//     CombinedAuthMiddleware::class,
//     PermissionsMiddleware::class => ['node' => 'export.csv'],
//     ExportController::class,
// ]);
// 
// This would match permissions like 'export.*' or 'export.csv'

// ================================
// Development Only
// ================================

if (App::config('app.env') === 'local') {
    // OpenAPI schema for API documentation
    $router->get('/openapi.json', [OpenApiController::class]);

    $router->get('/stream', [
        RateLimitMiddleware::class => ['limit' => '10/1m'],
        StreamController::class,
    ]);
}
