<?php

namespace App\Controllers;

use BaseApi\Models\BaseModel;
use BaseApi\Controller;
use BaseApi\Http\JsonResponse;
use App\Models\ApiToken;

class ApiTokenController extends Controller
{
    /**
     * List all API tokens for the authenticated user
     */
    public function get(): JsonResponse
    {
        $user = $this->request->user;

        if (!$user) {
            return JsonResponse::unauthorized();
        }

        $tokens = ApiToken::where('user_id', '=', $user['id'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Remove sensitive data from response
        $tokenData = array_map(fn($token): array => [
            'id' => $token['id'],
            'name' => $token['name'],
            'expires_at' => $token['expires_at'],
            'last_used_at' => $token['last_used_at'],
            'created_at' => $token['created_at'],
        ], $tokens);

        return JsonResponse::ok([
            'tokens' => $tokenData
        ]);
    }

    /**
     * Create a new API token
     */
    public function post(): JsonResponse
    {
        $user = $this->request->user;

        if (!$user) {
            return JsonResponse::unauthorized();
        }

        // Validate input
        $errors = $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'expires_at' => ['nullable', 'date'],
        ]);

        if ($errors) {
            return JsonResponse::validationError('Validation failed', $errors);
        }

        // Generate token
        $plainToken = ApiToken::generateToken();
        $tokenHash = ApiToken::hashToken($plainToken);

        // Create token record
        $apiToken = new ApiToken();
        $apiToken->user_id = $user['id'];
        $apiToken->name = $this->name;
        $apiToken->token_hash = $tokenHash;
        $apiToken->expires_at = $this->expires_at ?? null;
        $apiToken->save();

        return JsonResponse::created([
            'token' => $plainToken, // Only shown once!
            'id' => $apiToken->id,
            'name' => $apiToken->name,
            'expires_at' => $apiToken->expires_at,
            'created_at' => $apiToken->created_at,
        ]);
    }

    /**
     * Delete an API token
     */
    public function delete(): JsonResponse
    {
        $user = $this->request->user;

        if (!$user) {
            return JsonResponse::unauthorized();
        }

        // Find the token belonging to the user
        $token = ApiToken::where('id', '=', $this->id)
            ->where('user_id', '=', $user['id'])
            ->first();

        if (!$token instanceof BaseModel) {
            return JsonResponse::notFound('Token not found');
        }

        $token->delete();

        return JsonResponse::ok(['message' => 'Token deleted successfully']);
    }

    // Properties for parameter binding
    public string $name = '';

    public ?string $expires_at = null;

    public string $id = '';
}
