<?php

namespace BaseApi\Scripts;

require_once __DIR__ . '/../vendor/autoload.php';

use BaseApi\Modules\OpenAI;

$ai = new OpenAI();
foreach ($ai->stream('Tell me basic facts about Munich, Germany.') as $chunk) {
    if (isset($chunk['delta'])) {
        echo $chunk['delta'];
        flush();
    }
}
