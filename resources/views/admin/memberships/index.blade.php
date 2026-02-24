@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Memberships</h1>
                <p class="text-gray-600 mt-1">Manage membership tiers and members</p>
            </div>
            <div class="flex gap-3 flex-wrap">
                <a href="{{ route('admin.memberships.export') }}" class="btn-admin-secondary btn-admin-sm">
                    <i class="fas fa-download mr-2"></i>Export CSV
                </a>
                <a href="{{ route('admin.memberships.create') }}" class="btn-admin-primary">
                    <i class="fas fa-plus mr-2"></i>New Tier
                </a>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <p class="text-xs md:text-sm font-medium text-gray-600">Total Members</p>
            <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats->total) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <p class="text-xs md:text-sm font-medium text-green-600">Active</p>
            <p class="text-2xl md:text-3xl font-bold text-green-700 mt-2">{{ number_format($stats->active) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <p class="text-xs md:text-sm font-medium text-yellow-600">Past Due</p>
            <p class="text-2xl md:text-3xl font-bold text-yellow-700 mt-2">{{ number_format($stats->past_due) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <p class="text-xs md:text-sm font-medium text-red-600">Cancelled</p>
            <p class="text-2xl md:text-3xl font-bold text-red-700 mt-2">{{ number_format($stats->cancelled) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <p class="text-xs md:text-sm font-medium text-gray-600">Expired</p>
            <p class="text-2xl md:text-3xl font-bold text-gray-700 mt-2">{{ number_format($stats->expired) }}</p>
        </div>
    </div>

    {{-- Tiers Overview --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Membership Tiers</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($tiers as $tier)
                <div class="border rounded-lg p-4 {{ $tier->is_active ? 'border-gray-200' : 'border-gray-200 opacity-60' }}">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-gray-900">
                            <i class="fas fa-gem mr-1" style="color: {{ $tier->badge_color }};"></i>
                            {{ $tier->name }}
                        </h3>
                        @if(!$tier->is_active)
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded">Inactive</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600 mb-2">{{ $tier->formatted_price }} &middot; {{ $tier->discount_percentage }}% discount</p>
                    <p class="text-sm text-gray-500 mb-3">{{ $tier->active_members_count ?? $tier->active_member_count }} active members</p>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.memberships.edit', $tier) }}" class="btn-admin-secondary btn-admin-sm">Edit</a>
                        <a href="{{ route('admin.memberships.show', $tier) }}" class="btn-admin-secondary btn-admin-sm">View</a>
                        <form action="{{ route('admin.memberships.toggle-active', $tier) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="btn-admin-secondary btn-admin-sm">
                                {{ $tier->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Filters --}}
    <div class="hidden md:block bg-white shadow-sm rounded-lg mb-6">
        <form method="GET" action="{{ route('admin.memberships.index') }}" class="p-4 flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Members</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal text-sm"
                       placeholder="Name or email...">
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal text-sm">
                    <option value="">All</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="past_due" {{ request('status') == 'past_due' ? 'selected' : '' }}>Past Due</option>
                </select>
            </div>
            <div>
                <label for="tier" class="block text-sm font-medium text-gray-700 mb-1">Tier</label>
                <select name="tier" id="tier" class="rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal text-sm">
                    <option value="">All Tiers</option>
                    @foreach($tiers as $tier)
                        <option value="{{ $tier->id }}" {{ request('tier') == $tier->id ? 'selected' : '' }}>{{ $tier->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-admin-primary btn-admin-sm">Filter</button>
                <a href="{{ route('admin.memberships.index') }}" class="btn-admin-secondary btn-admin-sm">Clear</a>
            </div>
        </form>
    </div>

    {{-- Mobile Filter --}}
    <x-admin.mobile-filter-modal formAction="{{ route('admin.memberships.index') }}">
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" class="w-full rounded-md border-gray-300 shadow-sm text-sm" placeholder="Name or email...">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    <option value="">All</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="past_due" {{ request('status') == 'past_due' ? 'selected' : '' }}>Past Due</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tier</label>
                <select name="tier" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    <option value="">All Tiers</option>
                    @foreach($tiers as $tier)
                        <option value="{{ $tier->id }}" {{ request('tier') == $tier->id ? 'selected' : '' }}>{{ $tier->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </x-admin.mobile-filter-modal>

    {{-- Desktop Table --}}
    <div class="hidden md:block bg-white shadow-sm rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Member</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tier</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Started</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expires</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($members as $member)
                    @php
                        $statusBadge = [
                            'active' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                            'expired' => 'bg-gray-100 text-gray-800',
                            'past_due' => 'bg-yellow-100 text-yellow-800',
                        ][$member->status] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-gray-900">{{ $member->customer->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">{{ $member->customer->email ?? '' }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <i class="fas fa-gem mr-1" style="color: {{ $member->tier->badge_color ?? '#FF3366' }};"></i>
                            {{ $member->tier->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusBadge }}">
                                {{ ucfirst(str_replace('_', ' ', $member->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $member->starts_at?->format('M j, Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $member->expires_at?->format('M j, Y') ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-gem text-3xl mb-3 text-gray-300"></i>
                            <p>No members found.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Cards --}}
    <div class="grid grid-cols-1 gap-4 md:hidden">
        @forelse($members as $member)
            @php
                $statusBadge = [
                    'active' => 'bg-green-100 text-green-800',
                    'cancelled' => 'bg-red-100 text-red-800',
                    'expired' => 'bg-gray-100 text-gray-800',
                    'past_due' => 'bg-yellow-100 text-yellow-800',
                ][$member->status] ?? 'bg-gray-100 text-gray-800';
            @endphp
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-semibold text-gray-900">{{ $member->customer->name ?? 'N/A' }}</span>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusBadge }}">
                        {{ ucfirst(str_replace('_', ' ', $member->status)) }}
                    </span>
                </div>
                <p class="text-sm text-gray-600">{{ $member->customer->email ?? '' }}</p>
                <p class="text-sm text-gray-500 mt-1">
                    <i class="fas fa-gem mr-1" style="color: {{ $member->tier->badge_color ?? '#FF3366' }};"></i>
                    {{ $member->tier->name ?? 'N/A' }}
                </p>
                <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-100">
                    <span class="text-xs text-gray-400">Started {{ $member->starts_at?->format('M j, Y') }}</span>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-sm p-8 text-center text-gray-500">
                <i class="fas fa-gem text-3xl mb-3 text-gray-300"></i>
                <p>No members found.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $members->links() }}
    </div>
@endsection
