@extends('layouts.app')

@section('title', 'Manage Membership')

@section('content')
<div class="min-h-screen py-8" style="background: var(--surface);">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <nav class="flex items-center gap-2 text-sm mb-6" style="color: var(--on-surface-muted);">
            <a href="{{ route('dashboard') }}" class="hover:underline">Dashboard</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span style="color: var(--on-surface);">Membership</span>
        </nav>

        <h1 class="text-2xl md:text-3xl font-bold font-display mb-8" style="color: var(--on-surface);">My Membership</h1>

        @if($membership)
            {{-- Current Membership Card --}}
            <div class="card-glass rounded-2xl overflow-hidden mb-8">
                <div class="p-6" style="background: linear-gradient(135deg, {{ $membership->tier->badge_color }}15, {{ $membership->tier->badge_color }}05);">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div>
                            <p class="text-sm" style="color: var(--on-surface-muted);">Current Plan</p>
                            <h2 class="text-2xl font-bold font-display" style="color: var(--on-surface);">
                                <i class="fas fa-gem mr-2" style="color: {{ $membership->tier->badge_color }};"></i>
                                {{ $membership->tier->name }}
                            </h2>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $membership->is_active ? 'bg-green-100 text-green-800' : ($membership->status === 'past_due' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ ucfirst($membership->status) }}
                        </span>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium" style="color: var(--on-surface-muted);">Member Since</dt>
                            <dd class="mt-1 font-medium" style="color: var(--on-surface);">{{ $membership->starts_at->format('M j, Y') }}</dd>
                        </div>
                        @if($membership->expires_at)
                            <div>
                                <dt class="text-sm font-medium" style="color: var(--on-surface-muted);">{{ $membership->is_cancelled ? 'Access Until' : 'Next Billing' }}</dt>
                                <dd class="mt-1 font-medium" style="color: var(--on-surface);">{{ $membership->expires_at->format('M j, Y') }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium" style="color: var(--on-surface-muted);">Price</dt>
                            <dd class="mt-1 font-medium" style="color: var(--on-surface);">{{ $membership->tier->formatted_price }}</dd>
                        </div>
                        @if($membership->tier->discount_percentage > 0)
                            <div>
                                <dt class="text-sm font-medium" style="color: var(--on-surface-muted);">Member Discount</dt>
                                <dd class="mt-1 font-semibold text-earth-success">{{ number_format($membership->tier->discount_percentage) }}% off</dd>
                            </div>
                        @endif
                    </dl>

                    {{-- Benefits --}}
                    @if($membership->tier->features && count($membership->tier->features) > 0)
                        <div class="pt-4" style="border-top: 1px solid var(--glass-border);">
                            <h3 class="text-sm font-semibold mb-3" style="color: var(--on-surface);">Your Benefits</h3>
                            <ul class="space-y-2">
                                @if($membership->tier->discount_percentage > 0)
                                    <li class="flex items-center gap-2 text-sm" style="color: var(--on-surface);">
                                        <i class="fas fa-check-circle text-earth-success"></i>
                                        {{ number_format($membership->tier->discount_percentage) }}% discount on all purchases
                                    </li>
                                @endif
                                @if($membership->tier->priority_booking)
                                    <li class="flex items-center gap-2 text-sm" style="color: var(--on-surface);">
                                        <i class="fas fa-check-circle text-earth-success"></i>
                                        Priority appointment booking
                                    </li>
                                @endif
                                @if($membership->tier->free_shipping)
                                    <li class="flex items-center gap-2 text-sm" style="color: var(--on-surface);">
                                        <i class="fas fa-check-circle text-earth-success"></i>
                                        Free shipping on all orders
                                    </li>
                                @endif
                                @foreach($membership->tier->features as $feature)
                                    <li class="flex items-center gap-2 text-sm" style="color: var(--on-surface);">
                                        <i class="fas fa-check-circle text-earth-success"></i>
                                        {{ $feature }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Cancel Section --}}
                    @if($membership->is_active)
                        <div class="pt-4" style="border-top: 1px solid var(--glass-border);">
                            <form action="{{ route('memberships.cancel') }}" method="POST"
                                  onsubmit="return confirm('Are you sure you want to cancel your membership? You will retain access until {{ $membership->expires_at?->format('M j, Y') ?? 'the end of your billing period' }}.')">
                                @csrf
                                <button type="submit" class="text-sm text-red-500 hover:text-red-700 hover:underline">
                                    <i class="fas fa-times-circle mr-1"></i>Cancel Membership
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Upgrade/Switch Section --}}
            @if($tiers->where('id', '!=', $membership->membership_tier_id)->where('is_active', true)->count() > 0)
                <div class="card-glass rounded-2xl p-6">
                    <h2 class="text-lg font-semibold mb-4" style="color: var(--on-surface);">Switch Plans</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($tiers->where('id', '!=', $membership->membership_tier_id)->where('is_active', true) as $tier)
                            <div class="flex items-center justify-between p-4 rounded-xl" style="background: var(--surface-raised);">
                                <div>
                                    <p class="font-medium" style="color: var(--on-surface);">{{ $tier->name }}</p>
                                    <p class="text-sm" style="color: var(--on-surface-muted);">{{ $tier->formatted_price }}</p>
                                </div>
                                <form action="{{ route('memberships.subscribe', $tier) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-glass text-sm" style="color: var(--on-surface);">
                                        Switch
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        @else
            {{-- No Membership --}}
            <div class="card-glass rounded-2xl p-8 text-center">
                <i class="fas fa-gem text-4xl mb-4" style="color: var(--on-surface-muted);"></i>
                <h2 class="text-xl font-bold font-display mb-2" style="color: var(--on-surface);">No Active Membership</h2>
                <p class="mb-6" style="color: var(--on-surface-muted);">
                    Join a membership plan to unlock exclusive discounts and benefits.
                </p>
                <a href="{{ route('memberships.index') }}" class="btn-gradient">
                    View Plans
                </a>
            </div>
        @endif

        <div class="text-center mt-8">
            <a href="{{ route('dashboard') }}" class="text-sm hover:underline" style="color: var(--on-surface-muted);">
                <i class="fas fa-arrow-left mr-1"></i>Back to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
