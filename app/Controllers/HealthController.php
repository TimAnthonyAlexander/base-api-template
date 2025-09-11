<?php

namespace App\Controllers;

use BaseApi\Controllers\Controller;
use BaseApi\Http\JsonResponse;
use BaseApi\App;
use BaseApi\Database\DbException;

class HealthController extends Controller
{
    public string $db = '';

    public function get(): JsonResponse
    {
        $response = ['ok' => true];

        // Check if database check is requested
        if ($this->db === '1') {
            try {
                // Perform simple DB check
                $result = App::db()->scalar('SELECT 1');

                if ($result == 1) {
                    $response['db'] = true;

                    // Additional table check to confirm connectivity
                    App::db()->raw('SHOW TABLES');
                } else {
                    return JsonResponse::error('Database check failed', 500);
                }
            } catch (DbException) {
                return JsonResponse::error('Database connection failed', 500);
            }
        }

        return JsonResponse::ok($response);
    }

    public function post(): JsonResponse
    {
        return JsonResponse::ok(['ok' => true, 'received' => 'data']);
    }
}
