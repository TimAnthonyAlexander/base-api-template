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
        // Clear session data
        session_destroy();

        return JsonResponse::ok(['message' => 'Logged out']);
    }
}
