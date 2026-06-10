<?php

namespace App\Controllers;

use BaseApi\Controllers\Controller;
use BaseApi\Http\JsonResponse;
use BaseApi\Http\Attributes\ResponseType;
use BaseApi\Http\Attributes\Tag;

#[Tag('Authentication')]
class MeController extends Controller
{
    #[ResponseType(['user' => 'array'])]
    public function get(): JsonResponse
    {
        // CombinedAuthMiddleware authenticates via API token OR session and
        // attaches the resolved user to the request. Read it from here rather
        // than $_SESSION directly: on the Bearer-token path the SPA uses,
        // $_SESSION is never populated, so reading $_SESSION would 401 a
        // perfectly valid token request.
        $user = $this->request->user;

        if (!$user || empty($user['id'])) {
            return JsonResponse::error('Not authenticated', 401);
        }

        return JsonResponse::ok(['user' => $user]);
    }
}
