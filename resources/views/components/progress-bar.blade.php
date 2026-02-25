@props(['data'])

@php
    $totalRaised = $data['total_raised'] ?? 0;
    $goal = $data['goal'] ?? 15000;
    $percentage = $data['percentage'] ?? 0;
    $nextMilestone = $data['next_milestone'] ?? null;
    $milestones = $data['milestones'] ?? collect();
@endphp

<div class="card-bento p-6"
     x-data="{
         shown: false,
         count: 0,
         target: {{ $totalRaised }},
         animateCounter() {
             const duration = 1500;
             const start = performance.now();
             const step = (timestamp) => {
                 const elapsed = timestamp - start;
                 const progress = Math.min(elapsed / duration, 1);
                 const eased = 1 - Math.pow(1 - progress, 3);
                 this.count = Math.floor(eased * this.target);
                 if (progress < 1) requestAnimationFrame(step);
             };
             requestAnimationFrame(step);
         }
     }"
     x-intersect.once="shown = true; animateCounter()">

    <div class="flex items-center justify-between mb-3">
        <h3 class="font-display text-xl font-bold" style="color: var(--on-surface);">Fundraising Progress</h3>
        <span class="text-sm font-semibold" style="color: var(--color-forest);">{{ number_format($percentage, 1) }}%</span>
    </div>

    {{-- Progress Bar with milestones --}}
    <div class="progress-bar-track mb-3 relative">
        <div class="progress-bar-fill"
             :style="shown ? 'width: {{ $percentage }}%' : 'width: 0%'"
             style="transition: width 1.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);"></div>
        {{-- Milestone markers --}}
        @if($milestones->isNotEmpty())
            @foreach($milestones as $m)
                @php $milestonePercent = ($goal > 0) ? min(($m->target_amount / $goal) * 100, 100) : 0; @endphp
                <div class="absolute top-0 bottom-0 w-0.5 z-10 {{ $m->is_reached ? 'opacity-100' : 'opacity-40' }}"
                     style="left: {{ $milestonePercent }}%; background-color: var(--color-gold);"
                     title="{{ $m->title }} — ${{ number_format($m->target_amount, 0) }}"></div>
            @endforeach
        @endif
    </div>

    {{-- Amount --}}
    <div class="flex items-center justify-between text-sm">
        <span class="font-bold text-lg" style="color: var(--color-forest);">
            $<span x-text="count.toLocaleString()">{{ number_format($totalRaised, 0) }}</span>
        </span>
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
