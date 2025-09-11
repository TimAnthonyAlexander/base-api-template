<?php

namespace App\Controllers;

use BaseApi\Controllers\Controller;
use BaseApi\Http\JsonResponse;
use BaseApi\App;
use BaseApi\Database\DbException;
use BaseApi\Database\Drivers\SqliteDriver;

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

                    if (App::db()->getConnection()->getDriver() instanceof SqliteDriver) {
                        App::db()->raw('SELECT name FROM sqlite_master WHERE type="table"');
                    } else {
                        App::db()->raw('SHOW TABLES');
                    }
                } else {
                    return JsonResponse::error('Database check failed', 500);
                }
            } catch (DbException $e) {
                return JsonResponse::error('Database connection failed: ' . $e, 500);
            }
        }

        return JsonResponse::ok($response);
    }

    public function post(): JsonResponse
    {
        return JsonResponse::ok(['ok' => true, 'received' => 'data']);
    }
}
