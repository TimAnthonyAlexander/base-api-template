<?php

declare(strict_types=1);

namespace App\Controllers;

use BaseApi\Controllers\Controller;
use BaseApi\Http\JsonResponse;
use BaseApi\Http\Response;
use BaseApi\OpenApi\OpenApiGenerator;
use BaseApi\OpenApi\OpenApiCache;
use Exception;

final class OpenApiController extends Controller
{
    public function get(): Response
    {
        try {
            $cache = new OpenApiCache();
            
            // Try to get cached OpenAPI spec
            $spec = $cache->get();
            
            if ($spec === null) {
                // Generate fresh OpenAPI spec
                $generator = new OpenApiGenerator();
                $spec = $generator->generate();
                
                // Cache the generated spec
                $cache->put($spec);
            }
            
            return new JsonResponse($spec);
        } catch (Exception $e) {
            return JsonResponse::error('Failed to generate OpenAPI specification: ' . $e->getMessage());
        }
    }
}
