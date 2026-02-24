@extends('layouts.admin')

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
        <p class="text-gray-600 mt-2">Welcome back! Here's what's happening with your store today.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-6 mb-8">
        <a href="{{ route('admin.orders.index') }}" class="bg-white rounded-lg shadow p-4 md:p-6 hover:shadow-lg transform hover:-translate-y-1 transition-all duration-200 cursor-pointer">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-gray-600 mb-1">Total Orders</p>
                    <p class="text-2xl md:text-3xl font-bold text-gray-900">{{ $stats['total_orders'] }}</p>
                </div>
                <div class="bg-indigo-100 rounded-full p-2 md:p-3">
                    <i class="fas fa-shopping-cart text-indigo-600 text-lg md:text-xl"></i>
                </div>
            </div>
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="bg-white rounded-lg shadow p-4 md:p-6 hover:shadow-lg transform hover:-translate-y-1 transition-all duration-200 cursor-pointer">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-gray-600 mb-1">Pending Orders</p>
                    <p class="text-2xl md:text-3xl font-bold text-yellow-600">{{ $stats['pending_orders'] }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-2 md:p-3">
                    <i class="fas fa-clock text-yellow-600 text-lg md:text-xl"></i>
                </div>
            </div>
        </a>
        <a href="{{ route('admin.orders.index') }}" class="bg-white rounded-lg shadow p-4 md:p-6 hover:shadow-lg transform hover:-translate-y-1 transition-all duration-200 cursor-pointer">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-gray-600 mb-1">Total Revenue</p>
                    <p class="text-2xl md:text-3xl font-bold text-green-600">${{ number_format($stats['total_revenue'], 2) }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-2 md:p-3">
                    <i class="fas fa-dollar-sign text-green-600 text-lg md:text-xl"></i>
                </div>
            </div>
        </a>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
            <a href="{{ route('admin.products.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg p-3 md:p-4 text-center transition-colors duration-200">
                <i class="fas fa-plus-circle text-xl md:text-2xl mb-2"></i>
                <p class="text-sm md:text-base font-semibold">Add Product</p>
            </a>
            <a href="{{ route('admin.blog.posts.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg p-3 md:p-4 text-center transition-colors duration-200">
                <i class="fas fa-plus-circle text-xl md:text-2xl mb-2"></i>
                <p class="text-sm md:text-base font-semibold">Add Blog Post</p>
            </a>
            <a href="{{ route('admin.about.edit') }}" class="bg-orange-600 hover:bg-orange-700 text-white rounded-lg p-3 md:p-4 text-center transition-colors duration-200">
                <i class="fas fa-user-edit text-xl md:text-2xl mb-2"></i>
                <p class="text-sm md:text-base font-semibold">Edit About Page</p>
            </a>
            <a href="{{ route('admin.orders.index') }}" class="bg-green-600 hover:bg-green-700 text-white rounded-lg p-3 md:p-4 text-center transition-colors duration-200">
                <i class="fas fa-shopping-cart text-xl md:text-2xl mb-2"></i>
                <p class="text-sm md:text-base font-semibold">View Orders</p>
            </a>
        </div>
    </div>

    <!-- Analytics Section -->
    <div class="mt-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Business Analytics</h2>
            <div class="flex items-center gap-2">
                {{-- Period Selector --}}
                <div class="flex bg-gray-100 rounded-lg p-1">
                    @foreach([7 => '7d', 30 => '30d', 90 => '90d'] as $days => $label)
                        <a href="{{ route('admin.dashboard', ['period' => $days]) }}"
                           class="px-3 py-1 text-sm font-medium rounded-md transition-colors {{ $period == $days ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
                {{-- Export --}}
                <a href="{{ route('admin.dashboard.export', ['period' => $period]) }}" class="btn-admin-secondary btn-admin-sm">
                    <i class="fas fa-download mr-1"></i>CSV
                </a>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Revenue Trend + AOV -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Revenue & AOV (Last {{ $period }} Days)</h3>
                <canvas id="revenueChart" style="max-height: 300px;"></canvas>
            </div>

            <!-- Order Status Distribution -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Order Status Distribution</h3>
                <canvas id="orderStatusChart" style="max-height: 300px;"></canvas>
            </div>
        </div>

        <!-- Revenue by Category + Customer Metrics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Revenue by Category -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Revenue by Category</h3>
                @if($revenueByCategory->isNotEmpty())
                    <canvas id="categoryChart" style="max-height: 300px;"></canvas>
                @else
                    <p class="text-gray-500 text-center py-8">No category data available</p>
                @endif
            </div>

            <!-- Customer Metrics -->
            <div class="space-y-3">
                <div class="bg-white rounded-lg shadow p-4 md:p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-xs md:text-sm font-medium text-gray-600">Total Customers</h3>
                        <i class="fas fa-users text-blue-500 text-lg md:text-xl"></i>
                    </div>
                    <p class="text-2xl md:text-3xl font-bold text-gray-900">{{ $totalCustomers }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4 md:p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-xs md:text-sm font-medium text-gray-600">New This Month</h3>
                        <i class="fas fa-user-plus text-green-500 text-lg md:text-xl"></i>
                    </div>
                    <p class="text-2xl md:text-3xl font-bold text-green-600">{{ $newCustomersThisMonth }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4 md:p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-xs md:text-sm font-medium text-gray-600">Repeat Customer Rate</h3>
                        <i class="fas fa-sync-alt text-purple-500 text-lg md:text-xl"></i>
                    </div>
                    <p class="text-2xl md:text-3xl font-bold text-purple-600">{{ $repeatCustomerRate }}%</p>
                </div>
            </div>
        </div>

        <!-- Top Selling Products -->
        <div class="grid grid-cols-1 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-bold text-gray-900">Top Selling Products</h3>
                </div>
                <div class="p-6">
                    @if($topProducts->count() > 0)
                        <div class="space-y-4">
                            @foreach($topProducts as $product)
                                <div class="flex items-center justify-between pb-4 border-b last:border-b-0">
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900">{{ $product['name'] }}</p>
                                        <p class="text-sm text-gray-600">{{ $product['quantity'] }} units sold</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-green-600">${{ number_format($product['revenue'], 2) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No product sales yet</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900">Recent Orders</h2>
                    <a href="{{ route('admin.orders.index') }}" class="btn-admin-primary btn-admin-sm">
                        View All <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if($recentOrders->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentOrders as $order)
                            <a href="{{ route('admin.orders.show', $order) }}" class="flex items-center justify-between pb-4 border-b last:border-b-0 hover:bg-gray-50 -mx-2 px-2 py-2 rounded transition-colors cursor-pointer">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $order->order_number }}</p>
                                    <p class="text-sm text-gray-600">{{ $order->customer->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $order->created_at->diffForHumans() }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-900">${{ number_format($order->total_amount, 2) }}</p>
                                    <span class="inline-block px-2 py-1 text-xs rounded
                                        {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">No orders yet</p>
                @endif
            </div>
        </div>

        <!-- Low Stock Products -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900">Low Stock Alert</h2>
                    <a href="{{ route('admin.products.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                        Manage Inventory <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if($lowStockProducts->count() > 0)
                    <div class="space-y-4">
                        @foreach($lowStockProducts as $product)
                            <div class="border rounded-lg p-4">
                                <p class="font-semibold text-gray-900 mb-1">{{ $product->name }}</p>
                                <p class="text-sm text-gray-600 mb-2">SKU: {{ $product->sku }}</p>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-red-600 font-semibold">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>{{ $product->stock_quantity }} left
                                    </span>
                                    <a href="{{ route('admin.products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-800 text-xs">
                                        Update <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">All products are well stocked</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Blog Management -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900">Blog Posts</h2>
                    <a href="{{ route('admin.blog.posts.index') }}" class="btn-admin-primary btn-admin-sm">
                        View All <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-center flex-1">
                        <p class="text-3xl font-bold text-gray-900">{{ \App\Models\BlogPost::count() }}</p>
                        <p class="text-sm text-gray-600">Total Posts</p>
                    </div>
                    <div class="text-center flex-1 border-l">
                        <p class="text-3xl font-bold text-green-600">{{ \App\Models\BlogPost::where('published', true)->count() }}</p>
                        <p class="text-sm text-gray-600">Published</p>
                    </div>
                    <div class="text-center flex-1 border-l">
                        <p class="text-3xl font-bold text-yellow-600">{{ \App\Models\BlogPost::where('published', false)->count() }}</p>
                        <p class="text-sm text-gray-600">Drafts</p>
                    </div>
                </div>
                <a href="{{ route('admin.blog.posts.create') }}" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>Create New Post
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900">Blog Categories</h2>
                    <a href="{{ route('admin.blog.categories.index') }}" class="btn-admin-primary btn-admin-sm">
                        View All <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <p class="text-3xl font-bold text-gray-900 text-center mb-2">{{ \App\Models\BlogCategory::count() }}</p>
                    <p class="text-sm text-gray-600 text-center">Total Categories</p>
                </div>
                <a href="{{ route('admin.blog.categories.create') }}" class="block w-full text-center bg-abs-primary hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>Create New Category
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Trend + AOV Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: @json($revenueDates),
                datasets: [
                    {
                        label: 'Revenue ($)',
                        data: @json($revenueValues),
                        borderColor: 'rgb(45, 96, 105)',
                        backgroundColor: 'rgba(45, 96, 105, 0.1)',
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y'
                    },
                    {
                        label: 'AOV ($)',
                        data: @json($aovValues),
                        borderColor: 'rgb(251, 146, 60)',
                        backgroundColor: 'rgba(251, 146, 60, 0.1)',
                        tension: 0.4,
                        fill: false,
                        borderDash: [5, 5],
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': $' + context.parsed.y.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        ticks: { callback: v => '$' + v }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        grid: { drawOnChartArea: false },
                        ticks: { callback: v => '$' + v }
                    }
                }
            }
        });
    }

    // Order Status Distribution Chart
    const orderStatusCtx = document.getElementById('orderStatusChart');
    if (orderStatusCtx) {
        const statusData = @json($orderStatusCounts);
        const labels = [];
        const data = [];
        const colors = {
            'pending': '#ef4444',
            'processing': '#f59e0b',
            'shipped': '#3b82f6',
            'delivered': '#10b981',
            'completed': '#059669',
            'cancelled': '#6b7280',
            'failed': '#dc2626'
        };
        const backgroundColors = [];

        for (const [status, count] of Object.entries(statusData)) {
            labels.push(status.charAt(0).toUpperCase() + status.slice(1));
            data.push(count);
            backgroundColors.push(colors[status] || '#9ca3af');
        }

        new Chart(orderStatusCtx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: backgroundColors,
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    // Revenue by Category Chart
    const categoryCtx = document.getElementById('categoryChart');
    if (categoryCtx) {
        const catData = @json($revenueByCategory);
        new Chart(categoryCtx, {
            type: 'bar',
            data: {
                labels: catData.map(c => c.category_name),
                datasets: [{
                    label: 'Revenue ($)',
                    data: catData.map(c => parseFloat(c.revenue)),
                    backgroundColor: [
                        'rgba(45, 96, 105, 0.8)',
                        'rgba(251, 146, 60, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(107, 114, 128, 0.8)',
                    ],
                    borderRadius: 4,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: { label: ctx => '$' + ctx.parsed.x.toFixed(2) }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { callback: v => '$' + v }
                    }
                }
            }
        });
    }
});
</script>
@endpush
