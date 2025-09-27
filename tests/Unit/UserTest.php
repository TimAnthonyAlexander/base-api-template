<?php

namespace App\Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function test_can_create_user_instance(): void
    {
        $user = new User();
        
        $this->assertInstanceOf(User::class, $user);
        $this->assertIsString($user->name);
        $this->assertIsString($user->email);
        $this->assertIsString($user->password);
        $this->assertIsBool($user->active);
    }

    public function test_default_values(): void
    {
        $user = new User();
        
        $this->assertEquals('', $user->name);
        $this->assertEquals('', $user->email);
        $this->assertEquals('', $user->password);
        $this->assertTrue($user->active);
    }

    public function test_password_verification(): void
    {
        $user = new User();
        $plainPassword = 'test123';
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
        
        $user->password = $hashedPassword;
        
        $this->assertTrue($user->checkPassword($plainPassword));
        $this->assertFalse($user->checkPassword('wrongpassword'));
    }

    public function test_indexes_configuration(): void
    {
        $this->assertArrayHasKey('email', User::$indexes);
        $this->assertEquals('unique', User::$indexes['email']);
    }
}
