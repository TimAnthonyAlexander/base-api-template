<?php

namespace App\Jobs;

use Override;
use Exception;
use Throwable;
use BaseApi\Queue\Job;

class CallExternalApiJob extends Job
{
    protected int $maxRetries = 3;

    protected int $retryDelay = 60; // seconds - longer delay for API calls

    /**
     * @param array<string, mixed> $data
     * @param array<int, string> $headers
     */
    public function __construct(
        private readonly string $endpoint,
        private readonly array $data,
        private readonly string $method = 'POST',
        private readonly array $headers = []
    ) {
        // Store API call parameters
    }

    #[Override]
    public function handle(): void
    {
        $response = $this->makeHttpRequest(
            $this->endpoint, 
            $this->data, 
            $this->method, 
            $this->headers
        );

        if ($response['status'] >= 400) {
            throw new Exception(
                sprintf('API call failed with status %s: %s', $response['status'], $response['body'])
            );
        }

        // Process successful response
        $this->processResponse($response);

        error_log('External API call completed successfully: ' . $this->endpoint);
    }

    /**
     * @param array<string, mixed> $data
     * @param array<int, string> $headers
     * @return array<string, mixed>
     */
    private function makeHttpRequest(string $endpoint, array $data, string $method, array $headers): array
    {
        // Initialize cURL
        $ch = curl_init();

        // Set basic cURL options
        curl_setopt_array($ch, [
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT => 'BaseAPI/1.0',
        ]);

        // Set method and data
        if (strtoupper($method) === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data !== []) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                $headers[] = 'Content-Type: application/json';
            }
        } elseif (strtoupper($method) === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            if ($data !== []) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                $headers[] = 'Content-Type: application/json';
            }
        }

        // Set headers
        if ($headers !== []) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        // Execute request
        $body = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($body === false || $error !== '' && $error !== '0') {
            throw new Exception('cURL error: ' . $error);
        }

        return [
            'status' => $status,
            'body' => $body,
        ];
    }

    /**
     * @param array<string, mixed> $response
     */
    private function processResponse(array $response): void
    {
        // Process the successful API response
        // This could involve:
        // - Parsing JSON response
        // - Storing data in database
        // - Triggering other jobs
        // - Sending notifications

        $responseData = json_decode((string) $response['body'], true);

        if ($responseData === null && json_last_error() !== JSON_ERROR_NONE) {
            error_log('API response is not valid JSON: ' . $response['body']);
            return;
        }

        // Log successful processing
        error_log("Processed API response: " . json_encode($responseData));

        // Example: Store important data or trigger follow-up actions
        // if (isset($responseData['user_id'])) {
        //     dispatch(new UpdateUserDataJob($responseData['user_id'], $responseData));
        // }
    }

    #[Override]
    public function failed(Throwable $throwable): void
    {
        error_log(sprintf('External API call failed to %s: ', $this->endpoint) . $throwable->getMessage());
        parent::failed($throwable);

        // Could dispatch a notification to admins about API failures
        // dispatch(new NotifyAdminsJob(
        //     "API Call Failed", 
        //     "Failed to call {$this->endpoint}: {$exception->getMessage()}"
        // ));
    }
}
