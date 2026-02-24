<x-app-layout>
    @section('title', 'Fundraising Progress')

    <section class="py-16 px-4" style="background-color: var(--surface);">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-12">
                <h1 class="page-heading text-gradient-nature mb-4">Fundraising Progress</h1>
                <p class="text-fluid-base" style="color: var(--on-surface-muted);">
                    Transparency matters. Here's exactly where the money comes from and where it goes.
                </p>
            </div>

            {{-- Progress Bar --}}
            <x-progress-bar :data="$progressData" />

            {{-- Revenue Breakdown --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mt-8 mb-12">
                <div class="card-bento text-center">
                    <div class="text-sm font-semibold mb-1" style="color: var(--on-surface-muted);">Donations</div>
                    <div class="text-2xl font-bold" style="color: var(--color-forest);">${{ number_format($revenueBreakdown['donations'], 0) }}</div>
                </div>
                <div class="card-bento text-center">
                    <div class="text-sm font-semibold mb-1" style="color: var(--on-surface-muted);">Merch Profit</div>
                    <div class="text-2xl font-bold" style="color: var(--color-orange);">${{ number_format($revenueBreakdown['merch_profit'], 0) }}</div>
                </div>
                <div class="card-bento text-center">
                    <div class="text-sm font-semibold mb-1" style="color: var(--on-surface-muted);">Sponsorships</div>
                    <div class="text-2xl font-bold" style="color: var(--color-gold);">${{ number_format($revenueBreakdown['sponsors'], 0) }}</div>
                </div>
            </div>

            {{-- Budget Breakdown --}}
            @if($breakdown->isNotEmpty())
            <div class="mb-12">
                <h2 class="font-display text-2xl font-bold mb-6" style="color: var(--on-surface);">Where the Money Goes</h2>
                <div class="space-y-3">
                    @foreach($breakdown as $item)
                    <div class="card-bento flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            @if($item->color)
                                <span class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: {{ $item->color }};"></span>
                            @endif
                            <span class="font-medium" style="color: var(--on-surface);">{{ $item->label }}</span>
                        </div>
                        <span class="font-bold" style="color: var(--on-surface);">${{ number_format($item->amount, 0) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Milestones --}}
            @if($milestones->isNotEmpty())
            <div>
                <h2 class="font-display text-2xl font-bold mb-6" style="color: var(--on-surface);">Milestones</h2>
                <div class="space-y-4">
                    @foreach($milestones as $milestone)
                    <div class="card-bento flex items-center gap-4 {{ $milestone->is_reached ? 'opacity-100' : 'opacity-60' }}">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center {{ $milestone->is_reached ? 'bg-green-100 text-green-600' : '' }}" style="{{ !$milestone->is_reached ? 'background-color: var(--surface-border); color: var(--on-surface-muted);' : '' }}">
                            @if($milestone->is_reached)
                                <i class="fas fa-check"></i>
                            @else
                                <i class="{{ $milestone->icon ?? 'fas fa-flag' }}"></i>
                            @endif
                        </div>
                        <div class="flex-1">
                            <div class="font-semibold" style="color: var(--on-surface);">{{ $milestone->title }}</div>
                            @if($milestone->description)
                            <div class="text-sm" style="color: var(--on-surface-muted);">{{ $milestone->description }}</div>
                            @endif
                        </div>
                        <div class="text-right">
                            <div class="font-bold" style="color: var(--color-forest);">${{ number_format($milestone->target_amount, 0) }}</div>
                            @if($milestone->is_reached && $milestone->reached_at)
                            <div class="text-xs" style="color: var(--on-surface-muted);">{{ $milestone->reached_at->format('M j, Y') }}</div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- CTA --}}
            <div class="text-center mt-12">
                <a href="{{ route('donate.index') }}" class="btn-donate btn-lg">
                    <i class="fas fa-heart mr-2"></i> Help Us Reach Our Goal
                </a>
            </div>
        </div>
    </section>
</x-app-layout>
