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
            $openApiGenerator = new OpenApiGenerator();
            $spec = $openApiGenerator->generate();

            return new JsonResponse($spec);
        } catch (Exception $exception) {
            return JsonResponse::error('Failed to generate OpenAPI specification: ' . $exception->getMessage());
        }
    }
}
