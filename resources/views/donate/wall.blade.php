<x-app-layout>
    @section('title', 'Donor Wall')

    <section class="py-16 px-4" style="background-color: var(--surface);">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-12">
                <h1 class="page-heading text-gradient-nature mb-4">Donor Wall</h1>
                <p style="color: var(--on-surface-muted);">
                    {{ $stats['total_donors'] }} supporters have raised ${{ number_format($stats['total_raised'], 0) }} so far.
                </p>
            </div>

            <div class="space-y-3">
                @foreach($donors as $donor)
                <div class="card-bento flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        @if($donor->tier && $donor->tier->badge_icon)
                            <span class="text-xl">{{ $donor->tier->badge_icon }}</span>
                        @endif
                        <div>
                            <span class="font-semibold" style="color: var(--on-surface);">{{ $donor->display_name_attribute }}</span>
                            @if($donor->tier)
                                <span class="badge-gradient ml-2 text-xs">{{ $donor->tier->name }}</span>
                            @endif
                        </div>
                    </div>
                    @if(!$donor->is_anonymous)
                    <span class="font-bold" style="color: var(--color-forest);">${{ number_format($donor->amount, 0) }}</span>
                    @endif
                </div>
                @endforeach
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('donate.index') }}" class="btn-donate btn-lg">
                    <i class="fas fa-heart mr-2"></i> Join Our Supporters
                </a>
            </div>
        </div>
    </section>
</x-app-layout>
