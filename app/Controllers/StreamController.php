<?php

declare(strict_types=1);

namespace App\Controllers;

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
            foreach ($openAI->stream($this->prompt) as $chunk) {
                if (isset($chunk['delta'])) {
                    echo "data: " . json_encode($chunk) . "\n\n";
                    flush();
                }
            }
        });
    }
}
