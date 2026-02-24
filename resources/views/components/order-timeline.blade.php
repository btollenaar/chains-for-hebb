@props(['order'])

@php
    $steps = [
        ['key' => 'ordered', 'label' => 'Ordered', 'icon' => 'fas fa-receipt'],
        ['key' => 'processing', 'label' => 'Processing', 'icon' => 'fas fa-cog'],
        ['key' => 'shipped', 'label' => 'Shipped', 'icon' => 'fas fa-truck'],
        ['key' => 'delivered', 'label' => 'Delivered', 'icon' => 'fas fa-check-circle'],
    ];

    $statusOrder = ['pending' => 0, 'processing' => 1, 'shipped' => 2, 'delivered' => 3, 'completed' => 3];
    $currentStep = $statusOrder[$order->fulfillment_status] ?? 0;
    $isCancelled = $order->fulfillment_status === 'cancelled';

    $timestamps = [
        'ordered' => $order->created_at,
        'processing' => $currentStep >= 1 ? ($order->updated_at) : null,
        'shipped' => $order->shipped_at,
        'delivered' => $order->delivered_at,
    ];
@endphp

@if($isCancelled)
    <div class="flex items-center justify-center p-4 rounded-xl bg-red-500/10">
        <i class="fas fa-times-circle text-red-500 mr-2"></i>
        <span class="font-semibold text-red-500">Order Cancelled</span>
    </div>
@else
    {{-- Desktop: Horizontal Timeline --}}
    <div class="hidden sm:block">
        <div class="flex items-center justify-between">
            @foreach($steps as $index => $step)
                @php
                    $isComplete = $index <= $currentStep;
                    $isActive = $index === $currentStep;
                @endphp
                <div class="flex flex-col items-center {{ $index < count($steps) - 1 ? 'flex-1' : '' }}">
                    <div class="flex items-center w-full">
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm
                                {{ $isComplete ? 'bg-earth-success text-white' : '' }}"
                                style="{{ !$isComplete ? 'background: var(--surface-raised); color: var(--on-surface-muted);' : '' }}">
                                <i class="{{ $step['icon'] }}"></i>
                            </div>
                            <p class="text-xs font-semibold mt-2 {{ $isActive ? 'text-earth-success' : '' }}"
                               style="{{ !$isActive ? 'color: var(--on-surface-muted);' : '' }}">
                                {{ $step['label'] }}
                            </p>
                            @if($isComplete && $timestamps[$step['key']])
                                <p class="text-xs mt-0.5" style="color: var(--on-surface-muted);">
                                    {{ $timestamps[$step['key']]->format('M j') }}
                                </p>
                            @endif
                        </div>
                        @if($index < count($steps) - 1)
                            <div class="flex-1 h-0.5 mx-3 mt-[-1.5rem] {{ $index < $currentStep ? 'bg-earth-success' : '' }}"
                                 style="{{ $index >= $currentStep ? 'background: var(--surface-raised);' : '' }}"></div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Mobile: Vertical Timeline --}}
    <div class="sm:hidden space-y-4">
        @foreach($steps as $index => $step)
            @php
                $isComplete = $index <= $currentStep;
                $isActive = $index === $currentStep;
            @endphp
            <div class="flex items-start gap-3">
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs
                        {{ $isComplete ? 'bg-earth-success text-white' : '' }}"
                        style="{{ !$isComplete ? 'background: var(--surface-raised); color: var(--on-surface-muted);' : '' }}">
                        <i class="{{ $step['icon'] }}"></i>
                    </div>
                    @if($index < count($steps) - 1)
                        <div class="w-0.5 h-6 mt-1 {{ $index < $currentStep ? 'bg-earth-success' : '' }}"
                             style="{{ $index >= $currentStep ? 'background: var(--surface-raised);' : '' }}"></div>
                    @endif
                </div>
                <div class="pt-1">
                    <p class="text-sm font-semibold {{ $isActive ? 'text-earth-success' : '' }}"
                       style="{{ !$isActive ? 'color: var(--on-surface-muted);' : '' }}">
                        {{ $step['label'] }}
                    </p>
                    @if($isComplete && $timestamps[$step['key']])
                        <p class="text-xs" style="color: var(--on-surface-muted);">
                            {{ $timestamps[$step['key']]->format('M j, g:i A') }}
                        </p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif

{{-- Tracking Info --}}
@if($order->tracking_number)
    <div class="mt-4 p-4 rounded-xl" style="background: var(--glass-bg); border: 1px solid var(--glass-border);">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--on-surface-muted);">Tracking</p>
                <p class="font-mono text-sm font-semibold" style="color: var(--on-surface);">
                    {{ strtoupper($order->tracking_carrier ?? '') }} {{ $order->tracking_number }}
                </p>
            </div>
            @if($order->tracking_url)
                <a href="{{ $order->tracking_url }}" target="_blank" rel="noopener"
                   class="btn-gradient text-sm px-4 py-2">
                    <i class="fas fa-external-link-alt mr-1"></i>Track Package
                </a>
            @endif
        </div>
    </div>
@endif
