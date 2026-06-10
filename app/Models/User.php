<?php

namespace App\Models;

use Override;
use BaseApi\Database\Relations\HasMany;
use BaseApi\Models\BaseModel;

class User extends BaseModel
{
    public string $name = '';

    public string $password = '';

    public string $email = '';

    public bool $active = true;

    public string $role = 'guest';

    /**
     * Define indexes for this model
     * @var array<string, string>
     */
    public static array $indexes = [
        'email' => 'unique',
    ];

    public function checkPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    /**
     * Serialize for API output. Overrides BaseModel::jsonSerialize() to strip
     * the password hash — BaseModel serializes every public property, so
     * without this the bcrypt hash leaks in every login/signup/me response and
     * in the `$request->user` payload the UserProvider builds.
     *
     * @return array<string, mixed>
     */
    #[Override]
    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();
        unset($data['password']);

        return $data;
    }

    public function apiTokens(): HasMany
    {
        return $this->hasMany(ApiToken::class);
    }
}
