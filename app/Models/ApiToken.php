<?php

namespace App\Models;

use BaseApi\Database\Relations\BelongsTo;
use BaseApi\Models\BaseModel;

class ApiToken extends BaseModel
{
    public string $user_id = '';
    
    public string $name = '';
    
    public string $token_hash = '';
    
    public ?string $expires_at = null;
    
    public ?string $last_used_at = null;
    
    /**
     * Define indexes for this model
     * @var array<string, string>
     */
    public static array $indexes = [
        'token_hash' => 'unique',
        'user_id' => 'index',
    ];
    
    /**
     * Define custom columns for this model
     * @var array<string, array<string, mixed>>
     */
    public static array $columns = [
        'token_hash' => ['type' => 'TEXT', 'length' => 255],
        'name' => ['type' => 'TEXT', 'length' => 100],
        'expires_at' => ['type' => 'TEXT', 'nullable' => true],
        'last_used_at' => ['type' => 'TEXT', 'nullable' => true],
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Check if token is expired
     */
    public function isExpired(): bool
    {
        if ($this->expires_at === null) {
            return false;
        }
        
        return strtotime($this->expires_at) < time();
    }
    
    /**
     * Update last used timestamp
     */
    public function updateLastUsed(): void
    {
        $this->last_used_at = date('Y-m-d H:i:s');
        $this->save();
    }
    
    /**
     * Generate a secure API token
     */
    public static function generateToken(): string
    {
        return bin2hex(random_bytes(32)); // 64 character hex string
    }
    
    /**
     * Hash a token for storage
     */
    public static function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }
    
    /**
     * Find token by plain text token
     */
    public static function findByToken(string $token): ?self
    {
        $hash = self::hashToken($token);
        return self::firstWhere('token_hash', '=', $hash);
    }
}
