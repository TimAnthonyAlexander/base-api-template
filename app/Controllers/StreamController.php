<?php

declare(strict_types=1);

namespace App\Controllers;

use Exception;
use BaseApi\Controllers\Controller;
use BaseApi\Http\StreamedResponse;
use BaseApi\Http\JsonResponse;
use BaseApi\Modules\OpenAI;

class StreamController extends Controller
{
    public string $prompt = '';

    public function get(): StreamedResponse|JsonResponse
    {
        // Validate that prompt is provided and not empty
        $this->validate([
            'prompt' => 'required|string|min:1',
        ]);

        $openAI = new OpenAI();

        return StreamedResponse::sse(function () use ($openAI): void {
            // Ignore user abort to complete the stream properly
            ignore_user_abort(true);
            
            try {
                foreach ($openAI->stream($this->prompt) as $chunk) {
                    // Check if connection is still alive
                    if (connection_aborted() !== 0) {
                        break;
                    }
                    
                    // Extract only the text delta from the response
                    if (isset($chunk['delta']) && is_string($chunk['delta'])) {
                        echo "data: " . json_encode(['content' => $chunk['delta']]) . "\n\n";

                        // Force immediate flush
                        if (ob_get_level() > 0) {
                            ob_flush();
                        }

                        flush();
                    }
                }
                
                // Send completion signal
                if (connection_aborted() === 0) {
                    echo "data: [DONE]\n\n";
                    if (ob_get_level() > 0) {
                        ob_flush();
                    }

                    flush();
                }
            } catch (Exception $exception) {
                // Send error to client
                echo "data: " . json_encode(['error' => $exception->getMessage()]) . "\n\n";
                if (ob_get_level() > 0) {
                    ob_flush();
                }

                flush();
            }
        });
    }
}
