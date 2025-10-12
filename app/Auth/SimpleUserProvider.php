<?php

declare(strict_types=1);

namespace App\Auth;

use Override;
use App\Models\User;
use BaseApi\Auth\UserProvider;
use Exception;

/**
 * Default UserProvider implementation using database or stub fallback.
 */
class SimpleUserProvider implements UserProvider
{
    /**
     * Resolve user by ID from database or return stub.
     *
     * @return array<string, mixed>|null
     */
    #[Override]
    public function byId(string $id): ?array
    {
        try {
            return User::firstWhere('id', '=', $id)?->jsonSerialize();
        } catch (Exception) {
            return null;
        }
    }

    #[Override]
    public function getRole(string $id): ?string
    {
        try {
            $user = User::firstWhere('id', '=', $id);
            return $user?->role ?? null;
        } catch (Exception) {
            return null;
        }
    }

    #[Override]
    public function setRole(string $id, string $role): bool
    {
        try {
            $user = User::firstWhere('id', '=', $id);
            if (!$user instanceof User) {
                return false;
            }

            $user->role = $role;
            $user->save();
            return true;
        } catch (Exception) {
            return false;
        }
    }
}
