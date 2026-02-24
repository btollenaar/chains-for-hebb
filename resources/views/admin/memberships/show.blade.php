@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <a href="{{ route('admin.memberships.index') }}" class="text-sm text-admin-teal hover:underline mb-2 inline-block">
            <i class="fas fa-arrow-left mr-1"></i>Back to Memberships
        </a>
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="fas fa-gem mr-2" style="color: {{ $tier->badge_color }};"></i>
                    {{ $tier->name }}
                </h1>
                <p class="text-gray-600 mt-1">{{ $tier->formatted_price }} &middot; {{ $tier->discount_percentage }}% discount</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.memberships.edit', $tier) }}" class="btn-admin-primary">
                    <i class="fas fa-edit mr-2"></i>Edit Tier
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Tier Details --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Tier Details</h2>
                <dl class="space-y-4">
                    @if($tier->description)
                        <div class="grid grid-cols-3 gap-4">
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="col-span-2 text-sm text-gray-900">{{ $tier->description }}</dd>
                        </div>
                    @endif
                    <div class="grid grid-cols-3 gap-4">
                        <dt class="text-sm font-medium text-gray-500">Price</dt>
                        <dd class="col-span-2 text-sm text-gray-900">{{ $tier->formatted_price }}</dd>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <dt class="text-sm font-medium text-gray-500">Discount</dt>
                        <dd class="col-span-2 text-sm font-bold text-gray-900">{{ $tier->discount_percentage }}%</dd>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <dt class="text-sm font-medium text-gray-500">Benefits</dt>
                        <dd class="col-span-2 text-sm text-gray-900">
                            <ul class="space-y-1">
                                @if($tier->priority_booking)
                                    <li><i class="fas fa-check text-green-500 mr-1"></i> Priority Booking</li>
                                @endif
                                @if($tier->free_shipping)
                                    <li><i class="fas fa-check text-green-500 mr-1"></i> Free Shipping</li>
                                @endif
                                @if($tier->features)
                                    @foreach($tier->features as $feature)
                                        <li><i class="fas fa-check text-green-500 mr-1"></i> {{ $feature }}</li>
                                    @endforeach
                                @endif
                            </ul>
                        </dd>
                    </div>
                    @if($tier->stripe_price_id)
                        <div class="grid grid-cols-3 gap-4">
                            <dt class="text-sm font-medium text-gray-500">Stripe Price ID</dt>
                            <dd class="col-span-2 text-sm text-gray-900 font-mono">{{ $tier->stripe_price_id }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Recent Members --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Recent Members</h2>
                @if($recentMembers->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentMembers as $member)
                            @php
                                $statusBadge = [
                                    'active' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                    'expired' => 'bg-gray-100 text-gray-800',
                                    'past_due' => 'bg-yellow-100 text-yellow-800',
                                ][$member->status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $member->customer->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">Joined {{ $member->starts_at?->format('M j, Y') }}</p>
                                </div>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusBadge }}">
                                    {{ ucfirst($member->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">No members for this tier yet.</p>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-sm font-bold text-gray-900 mb-3">Stats</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Total Members</dt>
                        <dd class="text-sm font-bold text-gray-900">{{ $tier->memberships_count }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Active Members</dt>
                        <dd class="text-sm font-bold text-green-600">{{ $tier->active_members_count }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Status</dt>
                        <dd class="text-sm font-bold {{ $tier->is_active ? 'text-green-600' : 'text-red-600' }}">
                            {{ $tier->is_active ? 'Active' : 'Inactive' }}
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-sm font-bold text-gray-900 mb-3">Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.memberships.edit', $tier) }}" class="block w-full btn-admin-secondary btn-admin-sm text-center">
                        <i class="fas fa-edit mr-2"></i>Edit Tier
                    </a>
                    <form action="{{ route('admin.memberships.toggle-active', $tier) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full btn-admin-secondary btn-admin-sm">
                            <i class="fas fa-{{ $tier->is_active ? 'eye-slash' : 'eye' }} mr-2"></i>
                            {{ $tier->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                    @if($tier->active_members_count == 0)
                        <form action="{{ route('admin.memberships.destroy', $tier) }}" method="POST"
                              onsubmit="return confirm('Delete this tier? This cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">
                                <i class="fas fa-trash mr-2"></i>Delete Tier
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
