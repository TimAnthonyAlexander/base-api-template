<?php

declare(strict_types=1);

namespace App\Controllers;

use BaseApi\Controllers\Controller;
use BaseApi\Http\JsonResponse;
use BaseApi\Http\Response;

final class OpenApiController extends Controller
{
    public function get(): Response
    {
        return new JsonResponse(['gay']);
    }
}
