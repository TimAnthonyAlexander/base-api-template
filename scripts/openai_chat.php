<?php

declare(strict_types=1);

namespace BaseApi\Scripts;

require_once __DIR__ . '/../vendor/autoload.php';

use BaseApi\Modules\OpenAI;

$ai = new OpenAI();
$ai = $ai->model('gpt-4.1-nano');

$system = "You are a concise, helpful assistant. Keep answers short and correct.";
$history = [];

function build_prompt(string $system, array $history, string $user): string
{
    $lines = [];
    $lines[] = "System: " . $system;
    foreach ($history as $turn) {
        $lines[] = ($turn['role'] === 'user' ? "User: " : "Assistant: ") . $turn['text'];
    }
    $lines[] = "User: " . $user;
    $lines[] = "Assistant:";
    return implode("\n", $lines);
}

$stdin = fopen('php://stdin', 'r');
if ($stdin === false) {
    fwrite(STDERR, "Failed to open STDIN\n");
    exit(1);
}

echo "Chat ready. Type your message. Commands: /reset, /exit\n";

while (true) {
    echo "\n> ";
    flush();
    $user = fgets($stdin);
    if ($user === false) {
        echo "\n";
        break;
    }
    $user = trim($user);
    if ($user === '') {
        continue;
    }
    if ($user === '/exit') {
        echo "Bye.\n";
        break;
    }
    if ($user === '/reset') {
        $history = [];
        echo "(context cleared)\n";
        continue;
    }

    $prompt = build_prompt($system, $history, $user);

    $assistant = '';
    try {
        foreach ($ai->stream($prompt) as $chunk) {
            if (isset($chunk['delta']) && is_string($chunk['delta'])) {
                $piece = $chunk['delta'];
                $assistant .= $piece;
                echo $piece;
                flush();
            }
        }
        echo "\n";
    } catch (\Throwable $e) {
        fwrite(STDERR, "\n[error] " . $e->getMessage() . "\n");
        continue;
    }

    $history[] = ['role' => 'user', 'text' => $user];
    $history[] = ['role' => 'assistant', 'text' => $assistant];
}
