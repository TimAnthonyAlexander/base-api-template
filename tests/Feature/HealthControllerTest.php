<?php

namespace App\Tests\Feature;

use BaseApi\Testing\TestCase;

class HealthControllerTest extends TestCase
{
    public function test_health_endpoint_returns_ok(): void
    {
        $this->get('/health')
            ->assertStatus(200)
            ->assertJsonPath('ok', true);
    }

    public function test_health_endpoint_with_database_check(): void
    {
        $this->get('/health', ['db' => '1'])
            ->assertStatus(200)
            ->assertJsonPath('ok', true)
            ->assertJsonPath('db', true);
    }

    public function test_health_endpoint_with_cache_check(): void
    {
        $this->get('/health', ['cache' => '1'])
            ->assertStatus(200)
            ->assertJsonStructure([
                'ok',
                'cache' => [
                    'working',
                    'driver'
                ]
            ])
            ->assertJsonPath('cache.working', true);
    }
}
