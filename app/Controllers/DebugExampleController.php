<?php

namespace App\Controllers;

use BaseApi\Controllers\Controller;
use BaseApi\Http\JsonResponse;
use BaseApi\Http\Request;
use BaseApi\App;
use App\Models\User;

/**
 * Example controller demonstrating debug features
 * 
 * This controller shows how to use the BaseAPI debugging tools
 * in your application during development.
 * 
 * Enable debugging by:
 * 1. Setting APP_DEBUG=true in your .env file
 * 2. Adding ?debug=1 to your request URL
 * 3. Or configuring DEBUG_ENABLED=true
 */
class DebugExampleController extends Controller
{
    /**
     * Handle GET requests and route to appropriate debug example
     */
    public function get(): JsonResponse
    {
        // Route based on request path
        return match ($this->request->path) {
            '/debug/query' => $this->queryExample(),
            '/debug/profiling' => $this->profilingExample(),
            '/debug/exception' => $this->exceptionExample(),
            '/debug/slow-query' => $this->slowQueryExample(),
            '/debug/info' => $this->debugInfo(),
            default => JsonResponse::notFound('Debug endpoint not found')
        };
    }

    /**
     * Example endpoint showing automatic query logging
     */
    private function queryExample(): JsonResponse
    {
        // Queries are automatically logged when debug is enabled
        $userRows = User::where('id', '>', 0)->limit(5)->get();
        
        // Manual memory tracking
        App::profiler()->trackMemory('after_user_query');
        
        return JsonResponse::ok([
            'message' => 'Query example completed',
            'user_count' => count($userRows),
            'note' => 'Check the debug panel for query details'
        ]);
    }

    /**
     * Example showing manual profiling
     */
    private function profilingExample(): JsonResponse
    {
        // Manual profiling of a specific operation
        $result = App::profiler()->profile('expensive_operation', function() {
            // Simulate some work
            usleep(50000); // 50ms
            
            App::profiler()->trackMemory('during_work');
            
            return [
                'data' => range(1, 100),
                'processed' => true
            ];
        });
        
        // Track memory after operation
        App::profiler()->trackMemory('after_operation');
        
        return JsonResponse::ok([
            'message' => 'Profiling example completed',
            'result' => $result,
            'note' => 'Check debug data for timing and memory usage'
        ]);
    }

    /**
     * Example showing exception tracking
     */
    private function exceptionExample(): JsonResponse
    {
        try {
            // This will throw an exception that gets tracked
            $this->simulateError();
            
        } catch (\Exception $e) {
            // Exceptions are automatically logged, but you can add context
            App::profiler()->logException($e, [
                'controller' => 'DebugExampleController',
                'action' => 'exceptionExample',
                'user_input' => 'test_data'
            ]);
            
            return JsonResponse::error('Exception occurred (check debug data)', 500);
        }
        
        return JsonResponse::ok(['message' => 'No exception occurred']);
    }

    /**
     * Example showing multiple queries for slow query detection
     */
    private function slowQueryExample(): JsonResponse
    {
        // Multiple queries to demonstrate query counting
        $userCount = User::where('id', '>', 0)->count();
        
        // Another query for demonstration
        $activeUserCount = User::where('active', '=', true)->count();
        
        // Simulate a slow database query by adding a sleep in a real query
        // This will show up as a slow query in the profiler
        $slowQuery = App::profiler()->profile('slow_database_operation', function() {
            // Simulate slow query with SLEEP (works on MySQL) or delay logic
            try {
                return App::db()->raw("SELECT ?, SLEEP(0.15) as slow_operation", [1]);
            } catch (\Exception $e) {
                // Fallback for databases that don't support SLEEP
                usleep(150000); // 150ms
                return App::db()->raw("SELECT ? as slow_operation", [1]);
            }
        });
        
        // Additional queries to increase query count
        $testQuery1 = User::where('active', '=', true)->limit(1)->get();
        $testQuery2 = User::where('active', '=', false)->limit(1)->get();
        $testQuery3 = User::where('id', '>', 0)->limit(5)->get();
        
        return JsonResponse::ok([
            'message' => 'Slow query example completed',
            'total_users' => $userCount,
            'active_users' => $activeUserCount,
            'test_queries_executed' => 3,
            'slow_query_result' => !empty($slowQuery),
            'note' => 'This should show multiple queries and slow query detection'
        ]);
    }

    /**
     * Debug information endpoint
     * Returns current debug metrics as JSON
     */
    private function debugInfo(): JsonResponse
    {
        $summary = App::profiler()->getSummary();
        
        return JsonResponse::ok([
            'debug_enabled' => App::profiler()->isEnabled(),
            'app_debug' => App::config('app.debug', false),
            'debug_config' => App::config('debug.enabled', false),
            'environment' => App::config('app.env'),
            'current_metrics' => $summary,
            'memory_usage' => [
                'current_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
                'peak_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
            ]
        ]);
    }

    /**
     * Simulate an error for exception tracking demo
     */
    private function simulateError(): void
    {
        throw new \RuntimeException('This is a simulated error for debugging demonstration');
    }
}
