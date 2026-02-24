@extends('layouts.app')

@section('title', 'Membership Plans')

@section('content')
<div class="min-h-screen py-8" style="background: var(--surface);">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="text-center mb-12">
            <h1 class="text-3xl md:text-4xl font-bold font-display text-gradient mb-4">Membership Plans</h1>
            <p class="text-lg max-w-2xl mx-auto" style="color: var(--on-surface-muted);">
                Unlock exclusive discounts, priority booking, and more with a membership plan.
            </p>
        </div>

        @if($currentMembership)
            <div class="card-glass rounded-2xl p-6 mb-8 text-center" style="border-left: 4px solid {{ $currentMembership->tier->badge_color }};">
                <p class="text-sm" style="color: var(--on-surface-muted);">Your current plan</p>
                <p class="text-xl font-bold font-display" style="color: var(--on-surface);">
                    <i class="fas fa-gem mr-2" style="color: {{ $currentMembership->tier->badge_color }};"></i>
                    {{ $currentMembership->tier->name }}
                </p>
                <a href="{{ route('memberships.manage') }}" class="text-sm text-earth-primary hover:underline mt-2 inline-block">
                    Manage Membership <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        @endif

        {{-- Pricing Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($tiers as $tier)
                <div class="card-glass rounded-2xl overflow-hidden flex flex-col {{ $currentMembership && $currentMembership->membership_tier_id === $tier->id ? 'ring-2' : '' }}" style="{{ $currentMembership && $currentMembership->membership_tier_id === $tier->id ? 'border-color: ' . $tier->badge_color . '; --tw-ring-color: ' . $tier->badge_color : '' }}">
                    {{-- Tier Header --}}
                    <div class="p-6 text-center" style="background: linear-gradient(135deg, {{ $tier->badge_color }}15, {{ $tier->badge_color }}05);">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full mb-4" style="background: {{ $tier->badge_color }}20;">
                            <i class="fas fa-gem text-xl" style="color: {{ $tier->badge_color }};"></i>
                        </div>
                        <h2 class="text-xl font-bold font-display" style="color: var(--on-surface);">{{ $tier->name }}</h2>
                        @if($tier->description)
                            <p class="text-sm mt-2" style="color: var(--on-surface-muted);">{{ $tier->description }}</p>
                        @endif
                    </div>

                    {{-- Pricing --}}
                    <div class="px-6 py-4 text-center">
                        <div class="flex items-baseline justify-center gap-1">
                            <span class="text-4xl font-bold font-display" style="color: var(--on-surface);">${{ number_format($tier->price, 2) }}</span>
                            <span class="text-sm" style="color: var(--on-surface-muted);">/{{ $tier->billing_interval === 'yearly' ? 'year' : 'month' }}</span>
                        </div>
                        @if($tier->billing_interval === 'yearly')
                            <p class="text-sm mt-1 text-earth-success">
                                ${{ number_format($tier->monthly_equivalent, 2) }}/month equivalent
                            </p>
                        @endif
                        @if($tier->discount_percentage > 0)
                            <p class="text-sm mt-2 font-semibold" style="color: {{ $tier->badge_color }};">
                                {{ number_format($tier->discount_percentage) }}% off all orders
                            </p>
                        @endif
                    </div>

                    {{-- Features --}}
                    <div class="px-6 pb-4 flex-1">
                        <ul class="space-y-3">
                            @if($tier->discount_percentage > 0)
                                <li class="flex items-start gap-2 text-sm" style="color: var(--on-surface);">
                                    <i class="fas fa-check text-earth-success mt-0.5"></i>
                                    {{ number_format($tier->discount_percentage) }}% member discount on all purchases
                                </li>
                            @endif
                            @if($tier->priority_booking)
                                <li class="flex items-start gap-2 text-sm" style="color: var(--on-surface);">
                                    <i class="fas fa-check text-earth-success mt-0.5"></i>
                                    Priority appointment booking
                                </li>
                            @endif
                            @if($tier->free_shipping)
                                <li class="flex items-start gap-2 text-sm" style="color: var(--on-surface);">
                                    <i class="fas fa-check text-earth-success mt-0.5"></i>
                                    Free shipping on all orders
                                </li>
                            @endif
                            @if($tier->features)
                                @foreach($tier->features as $feature)
                                    <li class="flex items-start gap-2 text-sm" style="color: var(--on-surface);">
                                        <i class="fas fa-check text-earth-success mt-0.5"></i>
                                        {{ $feature }}
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>

                    {{-- Action --}}
                    <div class="px-6 pb-6">
                        @if($currentMembership && $currentMembership->membership_tier_id === $tier->id)
                            <span class="block w-full text-center py-3 rounded-xl text-sm font-semibold" style="background: {{ $tier->badge_color }}20; color: {{ $tier->badge_color }};">
                                <i class="fas fa-check-circle mr-1"></i> Current Plan
                            </span>
                        @elseif(Auth::check())
                            <form action="{{ route('memberships.subscribe', $tier) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-gradient w-full">
                                    {{ $currentMembership ? 'Switch Plan' : 'Get Started' }}
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="btn-gradient w-full block text-center">
                                Sign In to Subscribe
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16">
                    <i class="fas fa-gem text-4xl mb-4" style="color: var(--on-surface-muted);"></i>
                    <p style="color: var(--on-surface-muted);">No membership plans available at this time.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
