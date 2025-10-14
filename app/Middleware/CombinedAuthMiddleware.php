<?php

declare(strict_types=1);

namespace App\Middleware;

use Throwable;
use App\Models\ApiToken;
use Exception;
use Override;
use BaseApi\Http\Middleware;
use BaseApi\Http\Request;
use BaseApi\Http\Response;
use BaseApi\Http\JsonResponse;
use BaseApi\App;

/**
 * Middleware that supports both session-based and API token authentication.
 * Tries API token first, falls back to session auth.
 * This allows the same endpoint to work with both authentication methods.
 */
class CombinedAuthMiddleware implements Middleware
{
    #[Override]
    public function handle(Request $request, callable $next): Response
    {
        // First, try API token authentication
        $user = $this->tryApiTokenAuth($request);
        $authMethod = 'api_token';

        // If no token auth, try session auth
        if (!$user) {
            $user = $this->trySessionAuth($request);
            $authMethod = 'session';
        }

        // If neither method worked, return unauthorized
        if (!$user) {
            return JsonResponse::error('Unauthorized', 401);
        }

        // Attach user and auth method to request
        $request->user = $user;
        $request->authMethod = $authMethod;

        return $next($request);
    }

    /**
     * Try to authenticate via API token
     */
    private function tryApiTokenAuth(Request $request): ?array
    {
        $authHeader = null;
        foreach ($request->headers ?? [] as $k => $v) {
            if (strcasecmp((string)$k, 'authorization') === 0) {
                $authHeader = is_array($v) ? reset($v) : $v;
                break;
            }
        }

        if (!is_string($authHeader) || strncasecmp($authHeader, 'Bearer ', 7) !== 0) {
            return null;
        }

        $token = trim(substr($authHeader, 7));
        if ($token === '' || $token === '0') {
            return null;
        }

        try {
            $tokenModel = ApiToken::findByToken($token);
            if (!$tokenModel instanceof ApiToken || $tokenModel->isExpired()) {
                return null;
            }

            $userProvider = App::userProvider();
            $user = $userProvider->byId($tokenModel->user_id);
            if ($user) {
                $tokenModel->updateLastUsed();
            }

            return $user;
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Try to authenticate via session
     */
    private function trySessionAuth(Request $request): ?array
    {
        // Check if user_id is set in session
        if (!isset($request->session['user_id']) || empty($request->session['user_id'])) {
            return null;
        }

        try {
            // Resolve user using UserProvider
            $userProvider = App::userProvider();
            $user = $userProvider->byId($request->session['user_id']);

            if ($user === null) {
                // User ID in session but user doesn't exist - clear invalid session
                unset($request->session['user_id']);
                return null;
            }

            return $user;
        } catch (Exception) {
            return null;
        }
    }
}
