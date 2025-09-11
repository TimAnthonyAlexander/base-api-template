<?php

namespace App\Controllers;

use BaseApi\Controllers\Controller;
use BaseApi\Http\JsonResponse;
use BaseApi\Http\Attributes\ResponseType;
use BaseApi\Http\Attributes\Tag;
use BaseApi\App;

#[Tag('Authentication')]
class MeController extends Controller
{
    #[ResponseType(['user' => 'array'])]
    public function get(): JsonResponse
    {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            return JsonResponse::error('Not authenticated', 401);
        }

        // Use the user provider to get user details
        $user = App::userProvider()->byId($userId);

        if (!$user) {
            return JsonResponse::error('User not found', 404);
        }

        return JsonResponse::ok(['user' => $user]);
    }
}
