<?php

declare(strict_types=1);

namespace App\Controllers;

use BaseApi\Controllers\Controller;
use BaseApi\Http\StreamedResponse;
use BaseApi\Http\JsonResponse;
use BaseApi\Http\Response;
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

        $ai = new OpenAI();

        return StreamedResponse::sse(function () use ($ai) {
            foreach ($ai->stream($this->prompt) as $chunk) {
                // Extract only the text delta from the response
                if (isset($chunk['delta']) && is_string($chunk['delta'])) {
                    echo "data: " . json_encode(['content' => $chunk['delta']]) . "\n\n";
                    flush();
                }
            }
            
            // Send completion marker
            echo "data: [DONE]\n\n";
            flush();
        });
    }
}
