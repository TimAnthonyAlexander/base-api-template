<?php

namespace App\Models;

use BaseApi\Models\BaseModel;

class User extends BaseModel
{
    public string $name = '';
    public string $email = '';
    public bool $active = true;
    
    /**
     * Define indexes for this model
     * @var array<string, string>
     */
    public static array $indexes = [
        'email' => 'unique',
    ];
}
