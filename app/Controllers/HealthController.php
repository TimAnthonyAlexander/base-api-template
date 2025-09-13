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
        $spanId = App::profiler()->start('health_check_total');
        
        $responseSpanId = App::profiler()->start('response_initialization');
        $response = ['ok' => true];
        App::profiler()->stop($responseSpanId);

        // Check if database check is requested
        if ($this->db === '1') {
            $dbCheckSpanId = App::profiler()->start('database_health_check');
            
            try {
                // Perform simple DB check
                $scalarSpanId = App::profiler()->start('db_scalar_query', ['query' => 'SELECT 1']);
                $result = App::db()->scalar('SELECT 1');
                App::profiler()->stop($scalarSpanId);

                if ($result == 1) {
                    $response['db'] = true;

                    $tablesSpanId = App::profiler()->start('db_tables_query');
                    if (App::db()->getConnection()->getDriver() instanceof SqliteDriver) {
                        App::db()->raw('SELECT name FROM sqlite_master WHERE type="table"');
                    } else {
                        App::db()->raw('SHOW TABLES');
                    }
                    App::profiler()->stop($tablesSpanId);
                } else {
                    App::profiler()->stop($dbCheckSpanId);
                    App::profiler()->stop($spanId);
                    return JsonResponse::error('Database check failed', 500);
                }
            } catch (DbException $e) {
                App::profiler()->stop($dbCheckSpanId);
                App::profiler()->stop($spanId);
                return JsonResponse::error('Database connection failed: ' . $e, 500);
            }
            
            App::profiler()->stop($dbCheckSpanId);
        }

        $finalResponseSpanId = App::profiler()->start('final_response_creation');
        $jsonResponse = JsonResponse::ok($response);
        App::profiler()->stop($finalResponseSpanId);
        
        App::profiler()->stop($spanId);
        return $jsonResponse;
    }

    public function post(): JsonResponse
    {
        $spanId = App::profiler()->start('health_check_post');
        
        $responseSpanId = App::profiler()->start('post_response_creation');
        $jsonResponse = JsonResponse::ok(['ok' => true, 'received' => 'data']);
        App::profiler()->stop($responseSpanId);
        
        App::profiler()->stop($spanId);
        return $jsonResponse;
    }
}
