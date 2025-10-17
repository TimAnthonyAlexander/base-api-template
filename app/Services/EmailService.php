<?php

namespace App\Services;

use Throwable;
use BaseApi\Logger;
use BaseApi\Config;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

/**
 * Example email service to demonstrate dependency injection.
 * 
 * This service shows how dependencies are automatically injected
 * through constructor parameters.
 */
class EmailService
{
    public function __construct(
        private readonly Logger $logger,
        private readonly Config $config,
    ) {}

    /**
     * Send an email using Symfony Mailer.
     * Falls back to logging-only in non-production when no DSN is configured.
     *
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $body Plain text body; HTML is generated automatically
     * @return bool Success status
     */
    public function send(string $to, string $subject, string $body): bool
    {
        $this->logger->info(sprintf('Sending email to %s: %s', $to, $subject));

        $env = (string) ($this->config->get('app.env') ?? 'local');

        $dsn = $this->resolveDsn();
        if ($env !== 'production') {
            $this->logger->info('Email body: ' . $body);
            return true;
        }

        try {
            $transport = Transport::fromDsn($dsn);
            $mailer = new Mailer($transport);

            $fromAddress = (string)($this->config->get('mail.from.address') ?? 'noreply@localhost');
            $fromName = (string)($this->config->get('mail.from.name') ?? ($this->config->get('app.name') ?? 'BaseAPI'));

            $message = (new Email())
                ->from(sprintf('%s <%s>', $fromName, $fromAddress))
                ->to($to)
                ->subject($subject)
                ->text($body)
                ->html($this->buildHtml($subject, $body));

            $mailer->send($message);
            return true;
        } catch (Throwable $throwable) {
            $this->logger->error('Failed to send email: ' . $throwable->getMessage());
            return false;
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
        $appName = (string)($this->config->get('app.name') ?? 'BaseAPI');
        $subject = 'Welcome to ' . $appName;
        $body = "Hello {$name},\n\nWelcome to {$appName}!";

        return $this->send($email, $subject, $body);
    }

    private function resolveDsn(): string
    {
        $explicit = $this->config->get('mail.dsn');
        if (is_string($explicit) && $explicit !== '') {
            return $explicit;
        }

        $driver = (string)($this->config->get('mail.driver') ?? 'smtp');
        switch ($driver) {
            case 'null':
                return 'null://null';
            case 'sendmail':
                // Symfony supports sendmail://default, binary can be configured via php.ini
                return 'sendmail://default';
            case 'smtp':
            default:
                $host = (string)($this->config->get('mail.host') ?? 'localhost');
                $port = (int)($this->config->get('mail.port') ?? 25);
                $user = $this->config->get('mail.username');
                $pass = $this->config->get('mail.password');
                $encryption = $this->config->get('mail.encryption'); // tls|ssl|null
                $auth = '';
                if (is_string($user) && $user !== '') {
                    $auth = rawurlencode($user) . ':' . rawurlencode((string)$pass) . '@';
                }

                $query = '';
                if (is_string($encryption) && $encryption !== '' && $encryption !== 'null') {
                    $query = '?encryption=' . rawurlencode($encryption);
                }

                return sprintf('smtp://%s%s:%d%s', $auth, $host, $port, $query);
        }
    }

    private function buildHtml(string $subject, string $textBody): string
    {
        $safe = nl2br(htmlspecialchars($textBody, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
        $brand = (string)($this->config->get('app.name') ?? 'BaseAPI');
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>{$subject}</title>
  <style>
    body { background:#f6f9fc; margin:0; padding:0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Apple Color Emoji', 'Segoe UI Emoji', sans-serif; }
    .container { max-width:600px; margin:24px auto; background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.06); }
    .header { padding:16px 20px; background:#0f172a; color:#fff; font-weight:600; }
    .content { padding:20px; color:#111827; line-height:1.6; }
    .footer { padding:12px 20px; color:#6b7280; font-size:12px; text-align:center; }
  </style>
  <!--[if mso]>
  <style type="text/css">
    body, table, td { font-family: Arial, Helvetica, sans-serif !important; }
  </style>
  <![endif]-->
  </head>
  <body>
    <div class="container">
      <div class="header">{$brand}</div>
      <div class="content">{$safe}</div>
      <div class="footer">This is an automated message from {$brand}.</div>
    </div>
  </body>
</html>
HTML;
    }
}
