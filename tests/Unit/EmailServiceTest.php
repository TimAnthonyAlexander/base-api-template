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

    public function test_can_send_email_in_development(): void
    {
        $logger = $this->createMock(Logger::class);
        $config = $this->createMock(Config::class);
        
        $config->method('get')
            ->with('app.env')
            ->willReturn('development');

        $logger->expects($this->exactly(2))
            ->method('info');

        $emailService = new EmailService($logger, $config);
        $result = $emailService->send('test@example.com', 'Test Subject', 'Test Body');
        
        $this->assertTrue($result);
    }

    public function test_can_send_email_in_production(): void
    {
        $logger = $this->createMock(Logger::class);
        $config = $this->createMock(Config::class);
        
        $config->method('get')
            ->with('app.env')
            ->willReturn('production');

        $logger->expects($this->once())
            ->method('info');

        $emailService = new EmailService($logger, $config);
        $result = $emailService->send('test@example.com', 'Test Subject', 'Test Body');
        
        $this->assertTrue($result);
    }

    public function test_can_send_welcome_email(): void
    {
        $logger = $this->createMock(Logger::class);
        $config = $this->createMock(Config::class);
        
        $config->method('get')
            ->willReturnMap([
                ['app.env', null, 'development'],
                ['app.name', 'BaseAPI', 'MyApp']
            ]);

        $logger->expects($this->exactly(2))
            ->method('info');

        $emailService = new EmailService($logger, $config);
        $result = $emailService->sendWelcome('user@example.com', 'John Doe');
        
        $this->assertTrue($result);
    }

    public function test_welcome_email_uses_default_app_name_when_not_configured(): void
    {
        $logger = $this->createMock(Logger::class);
        $config = $this->createMock(Config::class);
        
        $config->method('get')
            ->willReturnMap([
                ['app.env', null, 'development'],
                ['app.name', 'BaseAPI', 'BaseAPI']
            ]);

        $logger->expects($this->exactly(2))
            ->method('info');

        $emailService = new EmailService($logger, $config);
        $result = $emailService->sendWelcome('user@example.com', 'Jane Doe');
        
        $this->assertTrue($result);
    }
}
