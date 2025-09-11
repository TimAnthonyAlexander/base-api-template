<?php

namespace App\Controllers;

use App\Models\User;
use BaseApi\Controllers\Controller;
use BaseApi\Http\JsonResponse;
use BaseApi\Http\Attributes\ResponseType;
use BaseApi\Http\Attributes\Tag;

/**
 * Minimal login endpoint for session authentication.
 * This is a stub - real credential validation is out of scope.
 */
#[Tag('Authentication')]
class LoginController extends Controller
{
    public string $email = '';
    public string $password = '';

    #[ResponseType(['user' => 'array'])]
    public function post(): JsonResponse
    {
        $this->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::firstWhere('email', '=', $this->email);

        if (!$user->checkPassword($this->password)) {
            return JsonResponse::error('Invalid credentials', 401);
        }

        $_SESSION['user_id'] = $user->id ?? null;

        // Regenerate session ID to mitigate fixation attacks
        session_regenerate_id(true);

        return JsonResponse::ok($user->jsonSerialize());
    }
}
