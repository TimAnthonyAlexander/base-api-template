<?php

namespace App\Controllers;

use App\Models\User;
use App\Services\EmailService;
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

    public function __construct(
        private readonly EmailService $emailService,
    ) {}

    #[ResponseType(User::class)]
    public function post(): JsonResponse
    {
        $this->validate([
            'name' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);

        // Check if user already exists
        $existingUser = User::firstWhere('email', '=', $this->email);
        if ($existingUser instanceof User) {
            return JsonResponse::error('User with this email already exists', 409);
        }

        // Create new user
        $user = new User();
        $user->name = $this->name;
        $user->email = $this->email;
        $user->password = password_hash($this->password, PASSWORD_DEFAULT);
        $user->role = 'user';
        $user->active = true;

        if (!$user->save()) {
            return JsonResponse::error('Failed to create user', 500);
        }

        // Send welcome email using injected service
        $this->emailService->sendWelcome($user->email, $user->name);

        // Log the user in automatically. Write $_SESSION directly: assigning to
        // $this->request->session only mutates a by-value copy on the Request
        // object, which PHP never persists — so the new user would be logged
        // out on their very next request.
        $_SESSION['user_id'] = $user->id;

        // Regenerate session ID for security (guarded so it does not warn when
        // no session is active, e.g. in tests).
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }

        return JsonResponse::created($user->jsonSerialize());
    }
}
