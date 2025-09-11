<?php

namespace App\Controllers;

use App\Models\User;
use BaseApi\Controllers\Controller;
use BaseApi\Http\JsonResponse;
use BaseApi\Http\Attributes\ResponseType;
use BaseApi\Http\Attributes\Tag;

/**
 * User registration endpoint.
 * Creates a new user account with email and password.
 */
#[Tag('Authentication')]
class SignupController extends Controller
{
    public string $name = '';
    public string $email = '';
    public string $password = '';

    #[ResponseType(['user' => 'array'])]
    public function post(): JsonResponse
    {
        $this->validate([
            'name' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);

        // Check if user already exists
        $existingUser = User::firstWhere('email', '=', $this->email);
        if ($existingUser) {
            return JsonResponse::error('User with this email already exists', 409);
        }

        // Create new user
        $user = new User();
        $user->name = $this->name;
        $user->email = $this->email;
        $user->password = password_hash($this->password, PASSWORD_DEFAULT);
        $user->active = true;

        if (!$user->save()) {
            return JsonResponse::error('Failed to create user', 500);
        }

        // Log the user in automatically
        $_SESSION['user_id'] = $user->id ?? null;

        // Regenerate session ID for security
        session_regenerate_id(true);

        return JsonResponse::ok($user->jsonSerialize());
    }
}
