<?php

declare(strict_types=1);

namespace App\Controllers;

use BaseApi\Controllers\Controller;
use BaseApi\Http\JsonResponse;
use BaseApi\Http\Response;
use BaseApi\OpenApi\OpenApiGenerator;
use Exception;

final class OpenApiController extends Controller
{
    public function get(): Response
    {
        try {
            // Generate fresh OpenAPI spec
            $generator = new OpenApiGenerator();
            $spec = $generator->generate();

            return new JsonResponse($spec);
        } catch (Exception $e) {
            return JsonResponse::error('Failed to generate OpenAPI specification: ' . $e->getMessage());
        }
    }
}
