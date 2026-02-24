@extends('layouts.app')

@section('title', 'Welcome to ' . $tier->name)

@section('content')
<div class="min-h-screen py-16" style="background: var(--surface);">
    <div class="max-w-lg mx-auto px-4 sm:px-6 text-center">
        <div class="card-glass rounded-2xl p-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full mb-6" style="background: {{ $tier->badge_color }}20;">
                <i class="fas fa-gem text-2xl" style="color: {{ $tier->badge_color }};"></i>
            </div>

            <h1 class="text-2xl font-bold font-display mb-2" style="color: var(--on-surface);">
                Welcome to {{ $tier->name }}!
            </h1>
            <p class="mb-6" style="color: var(--on-surface-muted);">
                Your membership is now active. Enjoy your exclusive benefits!
            </p>

            @if($tier->discount_percentage > 0)
                <div class="p-4 rounded-xl mb-6" style="background: var(--surface-raised);">
                    <p class="text-sm font-semibold text-earth-success">
                        <i class="fas fa-tag mr-1"></i>
                        {{ number_format($tier->discount_percentage) }}% member discount applied automatically at checkout
                    </p>
                </div>
            @endif

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('memberships.manage') }}" class="btn-gradient">
                    View My Membership
                </a>
                <a href="{{ route('products.index') }}" class="btn-glass" style="color: var(--on-surface);">
                    Start Shopping
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
