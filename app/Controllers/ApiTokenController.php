<?php

namespace App\Controllers;

use BaseApi\Models\BaseModel;
use BaseApi\Http\JsonResponse;
use App\Models\ApiToken;
use BaseApi\Controllers\Controller;
use BaseApi\Http\Attributes\ResponseType;
use BaseApi\Http\Attributes\Tag;

/**
 * API Token management endpoint.
 * Handles CRUD operations for API tokens used for authentication.
 */
#[Tag('Authentication')]
class ApiTokenController extends Controller
{
    public string $name = '';

    public ?string $expires_at = null;

    public string $id = '';

    /**
     * List all API tokens for the authenticated user
     */
    #[ResponseType(['tokens' => 'array'])]
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
        $tokenData = array_map(fn(ApiToken $apiToken): array => [
            'id' => $apiToken->id,
            'name' => $apiToken->name,
            'expires_at' => $apiToken->expires_at,
            'last_used_at' => $apiToken->last_used_at,
            'created_at' => $apiToken->created_at,
        ], $tokens);

        return JsonResponse::ok([
            'tokens' => $tokenData
        ]);
    }

    /**
     * Create a new API token
     */
    #[ResponseType(['token' => 'string', 'id' => 'string', 'name' => 'string', 'expires_at' => 'string|null', 'created_at' => 'string'])]
    public function post(): JsonResponse
    {
        $user = $this->request->user;

        if (!$user) {
            return JsonResponse::unauthorized();
        }

        // Validate input
        $this->validate([
            'name' => 'required|string|max:100',
        ]);

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
    #[ResponseType(['message' => 'string'])]
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
}
