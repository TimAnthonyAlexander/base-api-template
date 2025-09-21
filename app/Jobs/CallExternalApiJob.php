<?php

namespace App\Jobs;

use BaseApi\Queue\Job;

class CallExternalApiJob extends Job
{
    protected int $maxRetries = 3;
    protected int $retryDelay = 60; // seconds - longer delay for API calls
    
    public function __construct(
        private string $endpoint,
        private array $data,
        private string $method = 'POST',
        private array $headers = []
    ) {
        // Store API call parameters
    }
    
    public function handle(): void
    {
        $response = $this->makeHttpRequest(
            $this->endpoint, 
            $this->data, 
            $this->method, 
            $this->headers
        );
        
        if ($response['status'] >= 400) {
            throw new \Exception(
                "API call failed with status {$response['status']}: {$response['body']}"
            );
        }
        
        // Process successful response
        $this->processResponse($response);
        
        error_log("External API call completed successfully: {$this->endpoint}");
    }
    
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
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                $headers[] = 'Content-Type: application/json';
            }
        } elseif (strtoupper($method) === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                $headers[] = 'Content-Type: application/json';
            }
        }
        
        // Set headers
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        
        // Execute request
        $body = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        if ($body === false || !empty($error)) {
            throw new \Exception("cURL error: {$error}");
        }
        
        return [
            'status' => $status,
            'body' => $body,
        ];
    }
    
    private function processResponse(array $response): void
    {
        // Process the successful API response
        // This could involve:
        // - Parsing JSON response
        // - Storing data in database
        // - Triggering other jobs
        // - Sending notifications
        
        $responseData = json_decode($response['body'], true);
        
        if ($responseData === null && json_last_error() !== JSON_ERROR_NONE) {
            error_log("API response is not valid JSON: {$response['body']}");
            return;
        }
        
        // Log successful processing
        error_log("Processed API response: " . json_encode($responseData));
        
        // Example: Store important data or trigger follow-up actions
        // if (isset($responseData['user_id'])) {
        //     dispatch(new UpdateUserDataJob($responseData['user_id'], $responseData));
        // }
    }
    
    public function failed(\Throwable $exception): void
    {
        error_log("External API call failed to {$this->endpoint}: " . $exception->getMessage());
        parent::failed($exception);
        
        // Could dispatch a notification to admins about API failures
        // dispatch(new NotifyAdminsJob(
        //     "API Call Failed", 
        //     "Failed to call {$this->endpoint}: {$exception->getMessage()}"
        // ));
    }
}
