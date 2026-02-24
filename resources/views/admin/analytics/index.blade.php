@extends('layouts.admin')

@section('title', 'Analytics')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Order Analytics</h1>
            <p class="text-gray-600 mt-1">Revenue, products, and customer insights</p>
        </div>

        {{-- Period Selector --}}
        <div class="flex gap-2">
            <a href="{{ route('admin.analytics.index', ['period' => 30]) }}"
               class="{{ $period === 30 ? 'btn-admin-primary' : 'btn-admin-secondary' }}">
                30 Days
            </a>
            <a href="{{ route('admin.analytics.index', ['period' => 90]) }}"
               class="{{ $period === 90 ? 'btn-admin-primary' : 'btn-admin-secondary' }}">
                90 Days
            </a>
            <a href="{{ route('admin.analytics.index', ['period' => 365]) }}"
               class="{{ $period === 365 ? 'btn-admin-primary' : 'btn-admin-secondary' }}">
                365 Days
            </a>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-6 mb-8">
        {{-- Revenue --}}
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-gray-600">Revenue</p>
                    <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-2">${{ number_format($currentMetrics->total_revenue, 2) }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-2 md:p-3">
                    <i class="fas fa-dollar-sign text-green-600 text-lg md:text-xl"></i>
                </div>
            </div>
            <div class="mt-2">
                <span class="text-xs font-medium {{ $revenueDelta >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    <i class="fas fa-{{ $revenueDelta >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                    {{ abs($revenueDelta) }}%
                </span>
                <span class="text-xs text-gray-500">vs prev. period</span>
            </div>
        </div>

        {{-- Orders --}}
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-gray-600">Paid Orders</p>
                    <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-2">{{ number_format($currentMetrics->paid_orders) }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-2 md:p-3">
                    <i class="fas fa-shopping-bag text-blue-600 text-lg md:text-xl"></i>
                </div>
            </div>
            <div class="mt-2">
                <span class="text-xs font-medium {{ $ordersDelta >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    <i class="fas fa-{{ $ordersDelta >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                    {{ abs($ordersDelta) }}%
                </span>
                <span class="text-xs text-gray-500">vs prev. period</span>
            </div>
        </div>

        {{-- AOV --}}
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-gray-600">Avg Order Value</p>
                    <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-2">${{ number_format($avgOrderValue, 2) }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-2 md:p-3">
                    <i class="fas fa-chart-bar text-purple-600 text-lg md:text-xl"></i>
                </div>
            </div>
            <div class="mt-2">
                <span class="text-xs font-medium {{ $aovDelta >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    <i class="fas fa-{{ $aovDelta >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                    {{ abs($aovDelta) }}%
                </span>
                <span class="text-xs text-gray-500">vs prev. period</span>
            </div>
        </div>

        {{-- Discounts --}}
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-gray-600">Total Discounts</p>
                    <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-2">${{ number_format($currentMetrics->total_discounts, 2) }}</p>
                </div>
                <div class="bg-orange-100 rounded-full p-2 md:p-3">
                    <i class="fas fa-tag text-orange-600 text-lg md:text-xl"></i>
                </div>
            </div>
            <div class="mt-2">
                <span class="text-xs text-gray-500">Tax collected: ${{ number_format($currentMetrics->total_tax, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- Revenue Trend Chart --}}
    <div class="bg-white rounded-lg shadow-md p-4 md:p-6 mb-8">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Revenue Trend</h2>
        <div style="height: 300px;">
            <canvas id="revenueTrendChart"></canvas>
        </div>
    </div>

    {{-- Top Products --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- By Revenue --}}
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Top Products by Revenue</h2>
            @if($topProductsByRevenue->isEmpty())
                <p class="text-gray-500 text-sm">No product sales data for this period.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-2 font-medium text-gray-600">#</th>
                                <th class="text-left py-2 font-medium text-gray-600">Product</th>
                                <th class="text-right py-2 font-medium text-gray-600">Qty</th>
                                <th class="text-right py-2 font-medium text-gray-600">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topProductsByRevenue as $index => $product)
                            <tr class="border-b border-gray-100">
                                <td class="py-2 text-gray-500">{{ $index + 1 }}</td>
                                <td class="py-2 text-gray-900">{{ Str::limit($product->name, 30) }}</td>
                                <td class="py-2 text-right text-gray-700">{{ number_format($product->total_quantity) }}</td>
                                <td class="py-2 text-right font-medium text-gray-900">${{ number_format($product->revenue, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- By Quantity --}}
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Top Products by Quantity</h2>
            @if($topProductsByQuantity->isEmpty())
                <p class="text-gray-500 text-sm">No product sales data for this period.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-2 font-medium text-gray-600">#</th>
                                <th class="text-left py-2 font-medium text-gray-600">Product</th>
                                <th class="text-right py-2 font-medium text-gray-600">Qty</th>
                                <th class="text-right py-2 font-medium text-gray-600">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topProductsByQuantity as $index => $product)
                            <tr class="border-b border-gray-100">
                                <td class="py-2 text-gray-500">{{ $index + 1 }}</td>
                                <td class="py-2 text-gray-900">{{ Str::limit($product->name, 30) }}</td>
                                <td class="py-2 text-right font-medium text-gray-900">{{ number_format($product->total_quantity) }}</td>
                                <td class="py-2 text-right text-gray-700">${{ number_format($product->revenue, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Customer Metrics & Top Customers --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Customer Stats --}}
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Customer Metrics</h2>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600">New Customers</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($newCustomers) }}</p>
                    <p class="text-xs text-gray-500">Last {{ $period }} days</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600">Total Customers</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalCustomers) }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 col-span-2">
                    <p class="text-sm text-gray-600">Repeat Purchase Rate</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $repeatRate }}%</p>
                    <p class="text-xs text-gray-500">Customers with 2+ orders</p>
                </div>
            </div>
        </div>

        {{-- Top Customers --}}
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Top Customers by Spend</h2>
            @if($topCustomers->isEmpty())
                <p class="text-gray-500 text-sm">No customer data for this period.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-2 font-medium text-gray-600">Customer</th>
                                <th class="text-right py-2 font-medium text-gray-600">Orders</th>
                                <th class="text-right py-2 font-medium text-gray-600">Spent</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topCustomers as $customer)
                            <tr class="border-b border-gray-100">
                                <td class="py-2">
                                    <div class="text-gray-900">{{ $customer->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $customer->email }}</div>
                                </td>
                                <td class="py-2 text-right text-gray-700">{{ $customer->order_count }}</td>
                                <td class="py-2 text-right font-medium text-gray-900">${{ number_format($customer->total_spent, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Fulfillment & Payment Breakdown --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Fulfillment Status --}}
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Fulfillment Status</h2>
            @if(empty($fulfillmentBreakdown))
                <p class="text-gray-500 text-sm">No fulfillment data for this period.</p>
            @else
                <div class="space-y-3">
                    @php
                        $fulfillmentColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'processing' => 'bg-blue-100 text-blue-800',
                            'shipped' => 'bg-indigo-100 text-indigo-800',
                            'delivered' => 'bg-green-100 text-green-800',
                            'completed' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                        ];
                        $fulfillmentTotal = array_sum($fulfillmentBreakdown);
                    @endphp
                    @foreach($fulfillmentBreakdown as $status => $count)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $fulfillmentColors[$status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($status) }}
                            </span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-24 bg-gray-200 rounded-full h-2">
                                <div class="bg-admin-teal h-2 rounded-full" style="width: {{ $fulfillmentTotal > 0 ? round(($count / $fulfillmentTotal) * 100) : 0 }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-900 w-8 text-right">{{ $count }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Payment Methods --}}
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Payment Methods</h2>
            @if($paymentBreakdown->isEmpty())
                <p class="text-gray-500 text-sm">No payment data for this period.</p>
            @else
                <div class="space-y-3">
                    @php $paymentTotal = $paymentBreakdown->sum('count'); @endphp
                    @foreach($paymentBreakdown as $payment)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-900">{{ ucfirst($payment->payment_method) }}</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-sm text-gray-600">{{ $payment->count }} orders</span>
                            <span class="text-sm font-medium text-gray-900">${{ number_format($payment->total, 2) }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('revenueTrendChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($trendDates),
            datasets: [{
                label: 'Revenue',
                data: @json($trendValues),
                borderColor: '#2D6069',
                backgroundColor: 'rgba(45, 96, 105, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3,
                pointRadius: {{ $period <= 30 ? 3 : 0 }},
                pointHoverRadius: 5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '$' + context.parsed.y.toFixed(2);
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        maxTicksLimit: {{ $period <= 30 ? 15 : ($period <= 90 ? 12 : 12) }},
                        font: { size: 11 }
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        },
                        font: { size: 11 }
                    }
                }
            }
        }
    });
</script>
@endpush
