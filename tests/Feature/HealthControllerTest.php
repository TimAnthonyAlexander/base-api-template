<?php

namespace App\Tests\Feature;

use BaseApi\Testing\TestCase;

class HealthControllerTest extends TestCase
{
    public function test_health_endpoint_returns_ok(): void
    {
        $this->get('/health')
            ->assertStatus(200)
            ->assertJsonPath('data.ok', true);
    }

    public function test_health_endpoint_with_database_check(): void
    {
        $this->get('/health', ['db' => '1'])
            ->assertStatus(200)
            ->assertJsonPath('data.ok', true)
            ->assertJsonPath('data.db', true);
    }

    public function test_health_endpoint_with_cache_check(): void
    {
        $this->get('/health', ['cache' => '1'])
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'ok',
                    'cache' => [
                        'working',
                        'driver'
                    ]
                ]
            ])
            ->assertJsonPath('data.cache.working', true);
    }
}
