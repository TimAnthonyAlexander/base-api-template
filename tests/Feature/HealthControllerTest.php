<?php

namespace App\Tests\Feature;

use App\Controllers\HealthController;
use BaseApi\Http\JsonResponse;
use PHPUnit\Framework\TestCase;

class HealthControllerTest extends TestCase
{
    public function test_health_controller_can_be_instantiated(): void
    {
        $controller = new HealthController();
        
        $this->assertInstanceOf(HealthController::class, $controller);
        $this->assertEquals('', $controller->db);
        $this->assertEquals('', $controller->cache);
    }

    public function test_health_controller_post_endpoint(): void
    {
        $controller = new HealthController();
        
        $response = $controller->post();
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->status);
        $this->assertIsString($response->body);
    }

    public function test_health_controller_properties_can_be_set(): void
    {
        $controller = new HealthController();
        
        $controller->db = '1';
        $controller->cache = '1';
        
        $this->assertEquals('1', $controller->db);
        $this->assertEquals('1', $controller->cache);
    }
}
