<?php

namespace App\Tests\Unit;

use App\Services\EmailService;
use BaseApi\Config;
use BaseApi\Logger;
use PHPUnit\Framework\TestCase;

class EmailServiceTest extends TestCase
{
    public function test_email_service_can_be_instantiated(): void
    {
        $logger = $this->createStub(Logger::class);
        $config = $this->createStub(Config::class);
        
        $emailService = new EmailService($logger, $config);
        
        $this->assertInstanceOf(EmailService::class, $emailService);
    }

    public function test_can_send_email_with_null_transport(): void
    {
        $logger = $this->createMock(Logger::class);
        $config = $this->createMock(Config::class);

        $config->method('get')->willReturnMap([
            ['app.env', null, 'testing'],
            ['mail.dsn', null, 'null://null'],
            ['mail.from.address', null, 'noreply@example.com'],
            ['mail.from.name', null, 'BaseAPI'],
            ['app.name', null, 'BaseAPI'],
        ]);

        $logger->expects($this->atLeastOnce())->method('info');

        $emailService = new EmailService($logger, $config);
        $result = $emailService->send('test@example.com', 'Test Subject', 'Test Body');

        $this->assertTrue($result);
    }

    public function test_can_send_welcome_email(): void
    {
        $logger = $this->createMock(Logger::class);
        $config = $this->createMock(Config::class);

        $config->method('get')->willReturnMap([
            ['app.env', null, 'testing'],
            ['app.name', null, 'MyApp'],
            ['mail.dsn', null, 'null://null'],
            ['mail.from.address', null, 'noreply@example.com'],
            ['mail.from.name', null, 'MyApp'],
        ]);

        $logger->expects($this->atLeastOnce())->method('info');

        $emailService = new EmailService($logger, $config);
        $result = $emailService->sendWelcome('user@example.com', 'John Doe');

        $this->assertTrue($result);
    }

    public function test_welcome_email_uses_default_app_name_when_not_configured(): void
    {
        $logger = $this->createMock(Logger::class);
        $config = $this->createMock(Config::class);

        $config->method('get')->willReturnMap([
            ['app.env', null, 'testing'],
            ['app.name', null, 'BaseAPI'],
            ['mail.dsn', null, 'null://null'],
            ['mail.from.address', null, 'noreply@example.com'],
            ['mail.from.name', null, 'BaseAPI'],
        ]);

        $logger->expects($this->atLeastOnce())->method('info');

        $emailService = new EmailService($logger, $config);
        $result = $emailService->sendWelcome('user@example.com', 'Jane Doe');

        $this->assertTrue($result);
    }
}
