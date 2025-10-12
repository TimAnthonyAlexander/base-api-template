<?php

namespace App\Tests\Feature;

use BaseApi\Testing\TestCase;

/**
 * Example tests showcasing the fluent API testing syntax
 * 
 * These examples demonstrate various testing patterns you can use
 * in your BaseAPI application tests.
 */
class ApiTestExampleTest extends TestCase
{
    /**
     * Example: Basic GET request with status assertion
     */
    public function test_basic_get_request(): void
    {
        $this->get('/health')
            ->assertOk(); // Shorthand for assertStatus(200)
    }

    /**
     * Example: GET request with query parameters
     */
    public function test_get_request_with_query_params(): void
    {
        $this->get('/health', ['db' => '1', 'cache' => '1'])
            ->assertStatus(200)
            ->assertJsonHas('data')
            ->assertJsonPath('data.db', true);
    }

    /**
     * Example: Testing JSON structure
     */
    public function test_json_structure(): void
    {
        $this->get('/health', ['cache' => '1'])
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'ok',
                    'cache' => [
                        'working',
                        'driver'
                    ]
                ]
            ]);
    }

    /**
     * Example: Testing specific JSON path
     */
    public function test_json_path_value(): void
    {
        $this->get('/health')
            ->assertOk()
            ->assertJsonPath('data.ok', true);
    }

    /**
     * Example: Testing JSON fragment
     */
    public function test_json_fragment(): void
    {
        $this->get('/health')
            ->assertOk()
            ->assertJsonFragment(['data' => ['ok' => true]]);
    }

    /**
     * Example: Multiple assertion chain
     */
    public function test_multiple_assertions(): void
    {
        $this->get('/health')
            ->assertOk()
            ->assertJsonHas('data')
            ->assertJsonPath('data.ok', true)
            ->assertHeader('Content-Type', 'application/json; charset=utf-8');
    }

    /**
     * Example: Testing different HTTP methods
     */
    public function test_different_http_methods(): void
    {
        // GET - test actual routes
        $this->get('/health')->assertOk();
        
        // You can also test POST, PUT, PATCH, DELETE methods
        // Just ensure the routes and controllers are set up appropriately
    }

    /**
     * Example: Testing error responses
     */
    public function test_not_found_route(): void
    {
        $this->get('/non-existent-route')
            ->assertNotFound()
            ->assertJsonHas('error');
    }
}

