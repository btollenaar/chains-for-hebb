@props(['data'])

@php
    $totalRaised = $data['total_raised'] ?? 0;
    $goal = $data['goal'] ?? 15000;
    $percentage = $data['percentage'] ?? 0;
    $nextMilestone = $data['next_milestone'] ?? null;
@endphp

<div class="card-bento p-6">
    <div class="flex items-center justify-between mb-3">
        <h3 class="font-display text-xl font-bold" style="color: var(--on-surface);">Fundraising Progress</h3>
        <span class="text-sm font-semibold" style="color: var(--color-forest);">{{ number_format($percentage, 1) }}%</span>
    </div>

    {{-- Progress Bar --}}
    <div class="progress-bar-track mb-3">
        <div class="progress-bar-fill" style="width: {{ $percentage }}%;"></div>
    </div>

    {{-- Amount --}}
    <div class="flex items-center justify-between text-sm">
        <span class="font-bold text-lg" style="color: var(--color-forest);">${{ number_format($totalRaised, 0) }}</span>
        <span style="color: var(--on-surface-muted);">of ${{ number_format($goal, 0) }} goal</span>
    </div>

    {{-- Next Milestone --}}
    @if($nextMilestone)
    <div class="mt-4 pt-4 border-t" style="border-color: var(--surface-border);">
        <div class="flex items-center gap-2 text-sm">
            @if($nextMilestone->icon)
                <i class="{{ $nextMilestone->icon }}" style="color: var(--color-gold);"></i>
            @else
                <i class="fas fa-flag" style="color: var(--color-gold);"></i>
            @endif
            <span style="color: var(--on-surface-muted);">Next milestone:</span>
            <span class="font-semibold" style="color: var(--on-surface);">{{ $nextMilestone->title }}</span>
            <span style="color: var(--on-surface-muted);">at ${{ number_format($nextMilestone->target_amount, 0) }}</span>
        </div>
    </div>
    @endif
</div>
