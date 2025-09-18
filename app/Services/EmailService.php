<?php

namespace App\Services;

use BaseApi\Logger;
use BaseApi\Config;

/**
 * Example email service to demonstrate dependency injection.
 * 
 * This service shows how dependencies are automatically injected
 * through constructor parameters.
 */
class EmailService
{
    private Logger $logger;
    private Config $config;

    public function __construct(Logger $logger, Config $config)
    {
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * Send an email (mock implementation).
     * 
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $body Email body
     * @return bool Success status
     */
    public function send(string $to, string $subject, string $body): bool
    {
        // In a real implementation, this would send an actual email
        $this->logger->info("Sending email to {$to}: {$subject}");
        
        // Mock success based on app environment
        $isProduction = $this->config->get('app.env') === 'production';
        
        if ($isProduction) {
            // In production, actually send the email
            // return $this->actuallySemdEmail($to, $subject, $body);
            return true;
        } else {
            // In development, just log it
            $this->logger->info("Email body: {$body}");
            return true;
        }
    }

    /**
     * Send a welcome email to a new user.
     * 
     * @param string $email User email
     * @param string $name User name
     * @return bool Success status
     */
    public function sendWelcome(string $email, string $name): bool
    {
        $subject = "Welcome to " . $this->config->get('app.name', 'BaseAPI');
        $body = "Hello {$name},\n\nWelcome to our application!";
        
        return $this->send($email, $subject, $body);
    }
}

