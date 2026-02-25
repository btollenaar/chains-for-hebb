<x-app-layout>
    @section('title', 'Fundraising Progress')

    {{-- Hero Banner --}}
    <section class="relative py-24 px-4 overflow-hidden" style="margin-top: -72px; padding-top: calc(72px + 4rem); background: linear-gradient(135deg, #2D5016, #1A1A2E);">
        @if(file_exists(public_path('images/generated/progress-hero.webp')))
        <div class="absolute inset-0" style="background-image: url('{{ asset('images/generated/progress-hero.webp') }}'); background-size: cover; background-position: center; opacity: 0.25;"></div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-[var(--surface)]"></div>
        <div class="relative z-10 max-w-4xl mx-auto text-center">
            <h1 class="font-display text-white text-fluid-4xl font-bold uppercase tracking-tight mb-4">Fundraising Progress</h1>
            <p class="text-white/80 text-fluid-base max-w-2xl mx-auto">
                Transparency matters. Here's exactly where the money comes from and where it goes.
            </p>
        </div>
    </section>

    <section class="py-16 px-4" style="background-color: var(--surface);">
        <div class="max-w-4xl mx-auto">
            {{-- Progress Bar --}}
            <div data-animate="scale-in">
                <x-progress-bar :data="$progressData" />
            </div>

            {{-- Revenue Breakdown --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mt-8 mb-12" data-animate="stagger">
                <div class="card-bento text-center">
                    <div class="w-10 h-10 rounded-full mx-auto mb-3 flex items-center justify-center" style="background: rgba(45, 80, 22, 0.15);">
                        <i class="fas fa-heart" style="color: var(--color-forest);"></i>
                    </div>
                    <div class="text-sm font-semibold mb-1" style="color: var(--on-surface-muted);">Donations</div>
                    <div class="text-2xl font-bold" style="color: var(--color-forest);">${{ number_format($revenueBreakdown['donations'], 0) }}</div>
                </div>
                <div class="card-bento text-center">
                    <div class="w-10 h-10 rounded-full mx-auto mb-3 flex items-center justify-center" style="background: rgba(232, 93, 4, 0.15);">
                        <i class="fas fa-shopping-bag" style="color: var(--color-orange);"></i>
                    </div>
                    <div class="text-sm font-semibold mb-1" style="color: var(--on-surface-muted);">Merch Profit</div>
                    <div class="text-2xl font-bold" style="color: var(--color-orange);">${{ number_format($revenueBreakdown['merch_profit'], 0) }}</div>
                </div>
                <div class="card-bento text-center">
                    <div class="w-10 h-10 rounded-full mx-auto mb-3 flex items-center justify-center" style="background: rgba(139, 105, 20, 0.15);">
                        <i class="fas fa-handshake" style="color: var(--color-gold);"></i>
                    </div>
                    <div class="text-sm font-semibold mb-1" style="color: var(--on-surface-muted);">Sponsorships</div>
                    <div class="text-2xl font-bold" style="color: var(--color-gold);">${{ number_format($revenueBreakdown['sponsors'], 0) }}</div>
                </div>
            </div>

            {{-- Budget Breakdown --}}
            @if($breakdown->isNotEmpty())
            <div class="mb-12" data-animate="fade-up">
                <h2 class="font-display text-2xl font-bold mb-6" style="color: var(--on-surface);">Where the Money Goes</h2>
                @php $budgetTotal = $breakdown->sum('amount'); @endphp
                {{-- Stacked horizontal bar --}}
                <div class="h-8 rounded-full overflow-hidden flex mb-6" style="background: var(--surface-border);">
                    @foreach($breakdown as $item)
                        @php $itemPercent = $budgetTotal > 0 ? ($item->amount / $budgetTotal) * 100 : 0; @endphp
                        <div class="h-full relative group" style="width: {{ $itemPercent }}%; background-color: {{ $item->color ?? 'var(--color-forest)' }};" title="{{ $item->label }}: ${{ number_format($item->amount, 0) }}">
                            <div class="absolute inset-0 flex items-center justify-center text-white text-xs font-bold opacity-0 group-hover:opacity-100 transition-opacity">
                                {{ number_format($itemPercent, 0) }}%
                            </div>
                        </div>
                    @endforeach
                </div>
                {{-- Legend --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($breakdown as $item)
                    <div class="card-bento flex items-center justify-between py-3 px-4">
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: {{ $item->color ?? 'var(--color-forest)' }};"></span>
                            <span class="font-medium text-sm" style="color: var(--on-surface);">{{ $item->label }}</span>
                        </div>
                        <span class="font-bold text-sm" style="color: var(--on-surface);">${{ number_format($item->amount, 0) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Milestones — Visual Timeline --}}
            @if($milestones->isNotEmpty())
            <div data-animate="fade-up">
                <h2 class="font-display text-2xl font-bold mb-6" style="color: var(--on-surface);">Milestones</h2>
                <div class="milestone-timeline space-y-0">
                    @foreach($milestones as $milestone)
                    <div class="relative flex gap-4 pb-8 last:pb-0 {{ $milestone->is_reached ? 'milestone-reached' : '' }}">
                        {{-- Timeline line segment --}}
                        @if(!$loop->last)
                        <div class="absolute top-10 left-[19px] bottom-0 w-0.5 {{ $milestone->is_reached ? 'bg-gradient-to-b from-forest to-gold' : '' }}" style="{{ !$milestone->is_reached ? 'background-color: var(--surface-border);' : '' }}"></div>
                        @endif
                        {{-- Circle --}}
                        <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center z-10 {{ $milestone->is_reached ? 'bg-green-100 text-green-600 ring-4 ring-green-50' : '' }}" style="{{ !$milestone->is_reached ? 'background-color: var(--surface-border); color: var(--on-surface-muted);' : '' }}">
                            @if($milestone->is_reached)
                                <i class="fas fa-check text-sm"></i>
                            @else
                                <i class="{{ $milestone->icon ?? 'fas fa-flag' }} text-sm"></i>
                            @endif
                        </div>
                        {{-- Content --}}
                        <div class="flex-1 card-bento py-3 px-4 {{ $milestone->is_reached ? '' : 'opacity-60' }}">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-semibold" style="color: var(--on-surface);">{{ $milestone->title }}</div>
                                    @if($milestone->description)
                                    <div class="text-sm mt-0.5" style="color: var(--on-surface-muted);">{{ $milestone->description }}</div>
                                    @endif
                                </div>
                                <div class="text-right flex-shrink-0 ml-4">
                                    <div class="font-bold" style="color: var(--color-forest);">${{ number_format($milestone->target_amount, 0) }}</div>
                                    @if($milestone->is_reached && $milestone->reached_at)
                                    <div class="text-xs" style="color: var(--on-surface-muted);">{{ $milestone->reached_at->format('M j, Y') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- CTA --}}
            <div class="text-center mt-12" data-animate="scale-in">
                <a href="{{ route('donate.index') }}" class="btn-donate btn-lg btn-donate-pulse">
                    <i class="fas fa-heart mr-2"></i> Help Us Reach Our Goal
                </a>
            </div>
        </div>
    </section>
</x-app-layout>
