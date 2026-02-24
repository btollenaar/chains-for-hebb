<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Customer;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display audit log index with stats and filters.
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('user');

        // Filter by user
        if ($request->filled('user_id')) {
            $query->byUser($request->user_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->action($request->action);
        }

        // Filter by model type
        if ($request->filled('model_type')) {
            $query->forModel($request->model_type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from . ' 00:00:00');
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        // Search by model label or IP address
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('model_label', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Order by most recent
        $query->orderBy('created_at', 'desc');

        $logs = $query->paginate(25)->appends(request()->query());

        // Stats - Optimized: single query with selectRaw
        $statsQuery = AuditLog::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN DATE(created_at) = CURRENT_DATE THEN 1 ELSE 0 END) as today,
            SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as this_week,
            COUNT(DISTINCT user_id) as unique_users
        ", [now()->startOfWeek()->toDateTimeString()])->first();

        $stats = [
            'total' => $statsQuery->total ?? 0,
            'today' => $statsQuery->today ?? 0,
            'this_week' => $statsQuery->this_week ?? 0,
            'unique_users' => $statsQuery->unique_users ?? 0,
        ];

        // Get distinct users and model types for filter dropdowns
        $users = Customer::whereIn('id', AuditLog::select('user_id')->distinct())
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $modelTypes = AuditLog::select('model_type')
            ->distinct()
            ->orderBy('model_type')
            ->pluck('model_type');

        return view('admin.audit-logs.index', compact('logs', 'stats', 'users', 'modelTypes'));
    }

    /**
     * Display a single audit log entry detail with before/after diff.
     */
    public function show(AuditLog $auditLog)
    {
        $auditLog->load('user');

        return view('admin.audit-logs.show', compact('auditLog'));
    }
}
