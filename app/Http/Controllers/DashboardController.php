<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the dashboard based on user role.
     */
    public function index()
    {
        $user = Auth::user();

        // Admin users - redirect to admin dashboard
        if ($user->is_admin) {
            return redirect()->route('admin.dashboard')
                ->with('success', 'Welcome back, ' . $user->name . '!');
        }

        // Regular customers - show customer dashboard
        return $this->customerDashboard($user);
    }

    /**
     * Display the customer dashboard with stats and recent activity.
     */
    protected function customerDashboard($user)
    {
        // Calculate customer statistics
        $stats = [
            'total_orders' => Order::where('customer_id', $user->id)->count(),
            'total_spent' => Order::where('customer_id', $user->id)
                ->where('payment_status', 'paid')
                ->sum('total_amount'),
        ];

        // Get recent orders (with eager loading to prevent N+1)
        $recentOrders = Order::with('items')
            ->where('customer_id', $user->id)
            ->latest()
            ->take(3)
            ->get();

        return view('dashboard.index', compact(
            'stats',
            'recentOrders'
        ));
    }
}
