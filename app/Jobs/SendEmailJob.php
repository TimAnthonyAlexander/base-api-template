<?php

namespace App\Jobs;

use BaseApi\Queue\Job;
use App\Services\EmailService;
use BaseApi\App;
use BaseApi\Logger;

class SendEmailJob extends Job
{
    protected int $maxRetries = 3;
    protected int $retryDelay = 30; // seconds

    public function __construct(
        private string $to,
        private string $subject,
        private string $body,
        private ?string $from = null
    ) {}

    public function handle(): void
    {
        // Send email using email service
        $emailService = new EmailService(new Logger(), App::config());

        $emailService->send(
            to: $this->to,
            subject: $this->subject,
            body: $this->body,
        );

        // Log successful email
        error_log("Email sent successfully to {$this->to}: {$this->subject}");
    }

    public function failed(\Throwable $exception): void
    {
        // Handle failed email job
        error_log("Failed to send email to {$this->to}: " . $exception->getMessage());

        // Call parent to log the failure
        parent::failed($exception);

        // Could dispatch a notification job to admins about the failure
        // dispatch(new NotifyAdminsJob('Failed email', $exception->getMessage()));
    }
}
