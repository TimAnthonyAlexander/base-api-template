<?php

namespace App\Controllers;

use BaseApi\Controllers\Controller;
use BaseApi\Http\JsonResponse;

class BenchmarkController extends Controller
{
    public function get(): JsonResponse
    {
        return JsonResponse::ok([
            'message' => 'Hello, World!',
            'timestamp' => date('c'),
        ]);
    }
}
