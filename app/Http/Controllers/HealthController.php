<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $checks = [];
        $healthy = true;

        // Database connectivity
        try {
            DB::connection()->getPdo();
            $checks['database'] = 'ok';
        } catch (\Exception $e) {
            $checks['database'] = 'failed';
            $healthy = false;
        }

        // Storage symlink
        $checks['storage_link'] = file_exists(public_path('storage')) ? 'ok' : 'missing';
        if ($checks['storage_link'] !== 'ok') {
            $healthy = false;
        }

        // Cache read/write
        try {
            Cache::put('health_check', true, 10);
            $checks['cache'] = Cache::get('health_check') === true ? 'ok' : 'failed';
            Cache::forget('health_check');
        } catch (\Exception $e) {
            $checks['cache'] = 'failed';
            $healthy = false;
        }

        return response()->json([
            'status' => $healthy ? 'healthy' : 'degraded',
            'checks' => $checks,
        ], $healthy ? 200 : 503);
    }
}
