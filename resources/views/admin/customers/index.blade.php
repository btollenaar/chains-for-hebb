@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Customers</h1>
                <p class="text-gray-600 mt-1">Manage customer accounts and view their activity</p>
            </div>
        </div>
    </div>

    <div class="pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters Section - Desktop Only -->
            <div class="hidden md:block bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.customers.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <!-- Search Filter -->
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Name, Email, or Phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal">
                            </div>

                            <!-- Role Filter -->
                            <div>
                                <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                                <select name="role" id="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal">
                                    <option value="">All Roles</option>
                                    <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                            </div>

                            <!-- Tag Filter -->
                            <div>
                                <label for="tag" class="block text-sm font-medium text-gray-700">Tag</label>
                                <select name="tag" id="tag" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal">
                                    <option value="">All Tags</option>
                                    @foreach($tags as $tag)
                                        <option value="{{ $tag->slug }}" {{ request('tag') === $tag->slug ? 'selected' : '' }}>{{ $tag->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Sort By -->
                            <div>
                                <label for="sort_by" class="block text-sm font-medium text-gray-700">Sort By</label>
                                <select name="sort_by" id="sort_by" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal">
                                    <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Date Joined</option>
                                    <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>Name</option>
                                    <option value="email" {{ request('sort_by') === 'email' ? 'selected' : '' }}>Email</option>
                                </select>
                            </div>

                            <!-- Sort Order -->
                            <div>
                                <label for="sort_order" class="block text-sm font-medium text-gray-700">Order</label>
                                <select name="sort_order" id="sort_order" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal">
                                    <option value="desc" {{ request('sort_order') === 'desc' ? 'selected' : '' }}>Descending</option>
                                    <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>Ascending</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-2">
                            <button type="submit" class="btn-admin-primary">
                                <i class="fas fa-filter mr-2"></i>Apply Filters
                            </button>
                            <a href="{{ route('admin.customers.index') }}" class="btn-admin-secondary">
                                Clear Filters
                            </a>
                            <a href="{{ route('admin.customers.export', request()->query()) }}" class="btn-admin-success">
                                <i class="fas fa-download mr-2"></i>Export CSV
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Mobile Filter Modal -->
            <x-admin.mobile-filter-modal formAction="{{ route('admin.customers.index') }}">
                <!-- Search Filter -->
                <div>
                    <label for="mobile-search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" id="mobile-search" value="{{ request('search') }}"
                           placeholder="Name, Email, or Phone"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-admin-teal focus:border-admin-teal">
                </div>

                <!-- Role Filter -->
                <div>
                    <label for="mobile-role" class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <select name="role" id="mobile-role"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-admin-teal focus:border-admin-teal">
                        <option value="">All Roles</option>
                        <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>

                <!-- Tag Filter -->
                <div>
                    <label for="mobile-tag" class="block text-sm font-medium text-gray-700 mb-2">Tag</label>
                    <select name="tag" id="mobile-tag"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-admin-teal focus:border-admin-teal">
                        <option value="">All Tags</option>
                        @foreach($tags as $tag)
                            <option value="{{ $tag->slug }}" {{ request('tag') === $tag->slug ? 'selected' : '' }}>{{ $tag->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Sort By -->
                <div>
                    <label for="mobile-sort-by" class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                    <select name="sort_by" id="mobile-sort-by"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-admin-teal focus:border-admin-teal">
                        <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Date Joined</option>
                        <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>Name</option>
                        <option value="email" {{ request('sort_by') === 'email' ? 'selected' : '' }}>Email</option>
                    </select>
                </div>

                <!-- Sort Order -->
                <div>
                    <label for="mobile-sort-order" class="block text-sm font-medium text-gray-700 mb-2">Order</label>
                    <select name="sort_order" id="mobile-sort-order"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-admin-teal focus:border-admin-teal">
                        <option value="desc" {{ request('sort_order') === 'desc' ? 'selected' : '' }}>Descending</option>
                        <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>Ascending</option>
                    </select>
                </div>
            </x-admin.mobile-filter-modal>

            <!-- Customers Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($customers->count() > 0)
                        <!-- Mobile Cards View - Visible only on mobile -->
                        <div class="grid grid-cols-1 gap-4 md:hidden mb-6">
                            @foreach($customers as $customer)
                                <x-admin.table-card
                                    :item="$customer"
                                    route="admin.customers.show"
                                    :fields="[
                                        [
                                            'label' => 'Customer',
                                            'render' => function($item) {
                                                $html = '<div class=\'flex items-center\'>';
                                                $html .= '<div class=\'h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center mr-3\'><i class=\'fas fa-user text-gray-500\'></i></div>';
                                                $html .= '<div>';
                                                $html .= '<div class=\'font-medium text-gray-900\'>' . e($item->name) . '</div>';
                                                if ($item->is_admin) {
                                                    $html .= '<span class=\'inline-block px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800 mt-1\'>Admin</span>';
                                                }
                                                $html .= '</div></div>';
                                                return $html;
                                            }
                                        ],
                                        [
                                            'label' => 'Contact',
                                            'render' => function($item) {
                                                $html = '<div class=\'text-sm text-gray-900\'>' . e($item->email) . '</div>';
                                                if ($item->phone) {
                                                    $html .= '<div class=\'text-sm text-gray-500\'>' . e($item->phone) . '</div>';
                                                }
                                                return $html;
                                            }
                                        ],
                                        [
                                            'label' => 'Activity',
                                            'render' => function($item) {
                                                return '<div class=\'text-sm text-gray-900\'><span class=\'font-semibold\'>' . $item->orders_count . '</span> orders</div>';
                                            }
                                        ]
                                    ]"
                                    :actions="[
                                        ['route' => 'admin.customers.show', 'icon' => 'fa-eye', 'color' => 'blue', 'label' => 'View customer details']
                                    ]"
                                />
                            @endforeach
                        </div>

                        <!-- Desktop Table - Hidden on mobile -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                        <th class="hidden lg:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($customers as $customer)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                        <i class="fas fa-user text-gray-500"></i>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">{{ $customer->name }}</div>
                                                        @if($customer->is_admin)
                                                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">Admin</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $customer->email }}</div>
                                                @if($customer->phone)
                                                    <div class="text-sm text-gray-500">{{ $customer->phone }}</div>
                                                @endif
                                            </td>
                                            <td class="hidden lg:table-cell px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $roleClasses = [
                                                        'customer' => 'bg-blue-100 text-blue-800',
                                                        'admin' => 'bg-red-100 text-red-800',
                                                    ];
                                                @endphp
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $roleClasses[$customer->role ?? 'customer'] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $customer->role ?? 'customer')) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <span class="font-semibold">{{ $customer->orders_count }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $customer->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('admin.customers.show', $customer) }}"
                                                   aria-label="View customer details"
                                                   class="link-admin-info">
                                                    <i class="fas fa-eye" aria-hidden="true"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $customers->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No customers found</h3>
                            <p class="text-gray-500">
                                @if(request()->hasAny(['search', 'role', 'sort_by', 'sort_order']))
                                    No customers match your current filters. Try adjusting your search criteria.
                                @else
                                    Customers will appear here when they register or make a purchase.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
