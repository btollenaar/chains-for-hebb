<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $period = (int) $request->input('period', 30);
        $period = in_array($period, [30, 90, 365]) ? $period : 30;

        $startDate = now()->subDays($period);
        $previousStartDate = now()->subDays($period * 2);
        $previousEndDate = now()->subDays($period);

        // Revenue Metrics (current period)
        $currentMetrics = Order::selectRaw("
            COUNT(CASE WHEN payment_status = 'paid' THEN 1 END) as paid_orders,
            COALESCE(SUM(CASE WHEN payment_status = 'paid' THEN total_amount ELSE 0 END), 0) as total_revenue,
            COALESCE(SUM(CASE WHEN payment_status = 'paid' THEN discount_amount ELSE 0 END), 0) as total_discounts,
            COALESCE(SUM(CASE WHEN payment_status = 'paid' THEN tax_amount ELSE 0 END), 0) as total_tax
        ")
            ->where('created_at', '>=', $startDate)
            ->first();

        $avgOrderValue = $currentMetrics->paid_orders > 0
            ? $currentMetrics->total_revenue / $currentMetrics->paid_orders
            : 0;

        // Previous period metrics for comparison
        $previousMetrics = Order::selectRaw("
            COUNT(CASE WHEN payment_status = 'paid' THEN 1 END) as paid_orders,
            COALESCE(SUM(CASE WHEN payment_status = 'paid' THEN total_amount ELSE 0 END), 0) as total_revenue
        ")
            ->where('created_at', '>=', $previousStartDate)
            ->where('created_at', '<', $previousEndDate)
            ->first();

        $previousAvgOrderValue = $previousMetrics->paid_orders > 0
            ? $previousMetrics->total_revenue / $previousMetrics->paid_orders
            : 0;

        // Calculate deltas
        $revenueDelta = $previousMetrics->total_revenue > 0
            ? round((($currentMetrics->total_revenue - $previousMetrics->total_revenue) / $previousMetrics->total_revenue) * 100, 1)
            : ($currentMetrics->total_revenue > 0 ? 100 : 0);

        $ordersDelta = $previousMetrics->paid_orders > 0
            ? round((($currentMetrics->paid_orders - $previousMetrics->paid_orders) / $previousMetrics->paid_orders) * 100, 1)
            : ($currentMetrics->paid_orders > 0 ? 100 : 0);

        $aovDelta = $previousAvgOrderValue > 0
            ? round((($avgOrderValue - $previousAvgOrderValue) / $previousAvgOrderValue) * 100, 1)
            : ($avgOrderValue > 0 ? 100 : 0);

        // Revenue Trend (daily aggregation)
        $revenueTrend = Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue')
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fill in missing dates with 0
        $trendDates = [];
        $trendValues = [];
        for ($i = $period - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $trendDates[] = now()->subDays($i)->format('M d');
            $revenue = $revenueTrend->firstWhere('date', $date);
            $trendValues[] = $revenue ? (float) $revenue->revenue : 0;
        }

        // Top Products by Revenue
        $topProductsByRevenue = OrderItem::select(
                'order_items.item_id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.total) as revenue')
            )
            ->join('products', 'order_items.item_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('order_items.item_type', Product::class)
            ->where('orders.payment_status', 'paid')
            ->where('orders.created_at', '>=', $startDate)
            ->groupBy('order_items.item_id', 'products.name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        // Top Products by Quantity
        $topProductsByQuantity = OrderItem::select(
                'order_items.item_id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.total) as revenue')
            )
            ->join('products', 'order_items.item_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('order_items.item_type', Product::class)
            ->where('orders.payment_status', 'paid')
            ->where('orders.created_at', '>=', $startDate)
            ->groupBy('order_items.item_id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        // Customer Metrics
        $newCustomers = Customer::where('created_at', '>=', $startDate)->count();
        $totalCustomers = Customer::count();
        $customersWithMultipleOrders = Customer::has('orders', '>=', 2)->count();
        $repeatRate = $totalCustomers > 0
            ? round(($customersWithMultipleOrders / $totalCustomers) * 100, 1)
            : 0;

        // Top Customers by Spend
        $topCustomers = Customer::select('customers.id', 'customers.name', 'customers.email')
            ->selectRaw('COUNT(orders.id) as order_count')
            ->selectRaw('SUM(orders.total_amount) as total_spent')
            ->join('orders', 'customers.id', '=', 'orders.customer_id')
            ->where('orders.payment_status', 'paid')
            ->where('orders.created_at', '>=', $startDate)
            ->groupBy('customers.id', 'customers.name', 'customers.email')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        // Fulfillment Breakdown
        $fulfillmentBreakdown = Order::selectRaw('fulfillment_status, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('fulfillment_status')
            ->pluck('count', 'fulfillment_status')
            ->toArray();

        // Payment Method Breakdown
        $paymentBreakdown = Order::selectRaw('payment_method, COUNT(*) as count, SUM(total_amount) as total')
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', $startDate)
            ->groupBy('payment_method')
            ->get();

        return view('admin.analytics.index', compact(
            'period',
            'currentMetrics',
            'avgOrderValue',
            'revenueDelta',
            'ordersDelta',
            'aovDelta',
            'trendDates',
            'trendValues',
            'topProductsByRevenue',
            'topProductsByQuantity',
            'newCustomers',
            'totalCustomers',
            'repeatRate',
            'topCustomers',
            'fulfillmentBreakdown',
            'paymentBreakdown'
        ));
    }
}
