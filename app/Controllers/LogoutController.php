<?php

namespace App\Controllers;

use BaseApi\Controllers\Controller;
use BaseApi\Http\JsonResponse;
use BaseApi\Http\Attributes\ResponseType;
use BaseApi\Http\Attributes\Tag;

#[Tag('Authentication')]
class LogoutController extends Controller
{
    #[ResponseType(['message' => 'string'])]
    public function post(): JsonResponse
    {
        // Clear session data and destroy session
        // Handle both session and API token auth (CombinedAuthMiddleware)
        if (session_status() !== PHP_SESSION_ACTIVE) {
            // Session not active - might be using API token auth or session already closed
            // Clear the $_SESSION array just in case
            $_SESSION = [];
        } else {
            // Active session - destroy it properly
            $_SESSION = [];
            session_destroy();
        }

        return JsonResponse::ok(['message' => 'Logged out']);
    }
}
