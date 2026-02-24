@props(['active' => ''])

<div class="bg-white shadow-sm mb-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex flex-wrap gap-2 sm:gap-4 py-4">
            <a href="{{ route('dashboard') }}"
               class="px-4 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-abs-primary text-white' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                <i class="fas fa-home mr-2"></i>Overview
            </a>
            <a href="{{ route('orders.index') }}"
               class="px-4 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('orders.*') ? 'bg-abs-primary text-white' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                <i class="fas fa-shopping-bag mr-2"></i>Orders
            </a>
            <a href="{{ route('profile.edit') }}"
               class="px-4 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('profile.*') ? 'bg-abs-primary text-white' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                <i class="fas fa-user-cog mr-2"></i>Profile
            </a>
        </nav>
    </div>
</div>
