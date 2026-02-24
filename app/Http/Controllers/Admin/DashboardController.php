<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = (int) $request->input('period', 30);
        $period = in_array($period, [7, 30, 90]) ? $period : 30;
        $startDate = now()->subDays($period);

        // Basic Stats
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::pending()->count(),
            'total_revenue' => Order::paid()->sum('total_amount'),
        ];

        // Revenue Trend
        $revenueLast = Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue, COUNT(*) as order_count')
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $revenueDates = [];
        $revenueValues = [];
        $aovValues = [];
        for ($i = $period - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $revenueDates[] = now()->subDays($i)->format('M d');
            $dayData = $revenueLast->firstWhere('date', $date);
            $revenueValues[] = $dayData ? (float) $dayData->revenue : 0;
            $aovValues[] = $dayData && $dayData->order_count > 0
                ? round((float) $dayData->revenue / $dayData->order_count, 2)
                : 0;
        }

        // Order Status Distribution
        $orderStatusCounts = Order::selectRaw('fulfillment_status, COUNT(*) as count')
            ->groupBy('fulfillment_status')
            ->pluck('count', 'fulfillment_status')
            ->toArray();

        // Customer Metrics
        $totalCustomers = Customer::count();
        $newCustomersThisMonth = Customer::where('created_at', '>=', now()->startOfMonth())->count();
        $customersWithMultipleOrders = Customer::has('orders', '>=', 2)->count();
        $repeatCustomerRate = $totalCustomers > 0 ? round(($customersWithMultipleOrders / $totalCustomers) * 100, 1) : 0;

        // Top Selling Products
        $topProducts = OrderItem::select(
                'order_items.item_id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.total) as revenue')
            )
            ->join('products', 'order_items.item_id', '=', 'products.id')
            ->where('order_items.item_type', Product::class)
            ->groupBy('order_items.item_id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get()
            ->map(fn ($item) => [
                'name' => $item->name ?? 'Unknown',
                'quantity' => $item->total_quantity,
                'revenue' => $item->revenue ?? 0,
            ]);

        // Revenue by Category (via pivot table)
        $revenueByCategory = OrderItem::select(
                'product_categories.name as category_name',
                DB::raw('SUM(order_items.total) as revenue')
            )
            ->join('products', 'order_items.item_id', '=', 'products.id')
            ->join('product_product_category', function ($join) {
                $join->on('products.id', '=', 'product_product_category.product_id')
                     ->where('product_product_category.is_primary', true);
            })
            ->join('product_categories', 'product_product_category.product_category_id', '=', 'product_categories.id')
            ->where('order_items.item_type', Product::class)
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'paid')
            ->where('orders.created_at', '>=', $startDate)
            ->groupBy('product_categories.name')
            ->orderByDesc('revenue')
            ->limit(8)
            ->get();

        // Recent activity data
        $recentOrders = Order::with('customer')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $lowStockProducts = Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->where('stock_quantity', '>', 0)
            ->orderBy('stock_quantity')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'period',
            'revenueDates',
            'revenueValues',
            'aovValues',
            'orderStatusCounts',
            'totalCustomers',
            'newCustomersThisMonth',
            'repeatCustomerRate',
            'topProducts',
            'revenueByCategory',
            'recentOrders',
            'lowStockProducts'
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        $period = (int) $request->input('period', 30);
        $period = in_array($period, [7, 30, 90]) ? $period : 30;
        $startDate = now()->subDays($period);

        $data = Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue, COUNT(*) as order_count')
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $filename = 'dashboard-export-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($data, $period) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Revenue', 'Order Count', 'AOV']);

            for ($i = $period - 1; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $dayData = $data->firstWhere('date', $date);
                $revenue = $dayData ? (float) $dayData->revenue : 0;
                $count = $dayData ? (int) $dayData->order_count : 0;
                $aov = $count > 0 ? round($revenue / $count, 2) : 0;
                fputcsv($handle, [$date, $revenue, $count, $aov]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
