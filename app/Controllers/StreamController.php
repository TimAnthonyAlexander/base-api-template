<?php

declare(strict_types=1);

namespace App\Controllers;

use Generator;
use BaseApi\Controllers\Controller;
use BaseApi\Http\StreamedResponse;
use BaseApi\Http\StreamHelper;
use BaseApi\Modules\OpenAI;

class StreamController extends Controller
{
    public string $prompt = '';

    public function get(): StreamedResponse
    {
        $this->validate([
            'prompt' => 'required|string|min:1',
        ]);

        $openAI = new OpenAI();

        return StreamHelper::sse(
            fn(): Generator => $openAI->stream($this->prompt),
            StreamHelper::openAITextTransformer(...)
        );
    }
}
