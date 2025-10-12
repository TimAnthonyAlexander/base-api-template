<?php

namespace App\Jobs;

use Override;
use Throwable;
use BaseApi\Queue\Job;
use App\Services\EmailService;
use BaseApi\App;
use BaseApi\Logger;

class SendEmailJob extends Job
{
    protected int $maxRetries = 3;

    protected int $retryDelay = 30; // seconds

    public function __construct(
        private readonly string $to,
        private readonly string $subject,
        private readonly string $body
    ) {}

    #[Override]
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
        error_log(sprintf('Email sent successfully to %s: %s', $this->to, $this->subject));
    }

    #[Override]
    public function failed(Throwable $throwable): void
    {
        // Handle failed email job
        error_log(sprintf('Failed to send email to %s: ', $this->to) . $throwable->getMessage());

        // Call parent to log the failure
        parent::failed($throwable);

        // Could dispatch a notification job to admins about the failure
        // dispatch(new NotifyAdminsJob('Failed email', $exception->getMessage()));
    }
}
