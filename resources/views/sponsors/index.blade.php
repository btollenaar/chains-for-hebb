<x-app-layout>
    @section('title', 'Sponsors & Partners')

    <section class="py-16 px-4" style="background-color: var(--surface);">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-12">
                <h1 class="page-heading text-gradient-nature mb-4">Our Sponsors</h1>
                <p style="color: var(--on-surface-muted);" class="text-fluid-base">
                    These businesses and individuals are making the Hebb Park disc golf course possible.
                </p>
            </div>

            @foreach($tiers as $tier)
                @if($tier->sponsors->isNotEmpty())
                <div class="mb-12">
                    <h2 class="font-display text-2xl font-bold mb-6 text-center" style="color: var(--on-surface);">
                        {{ $tier->name }}
                        <span class="text-sm font-normal ml-2" style="color: var(--on-surface-muted);">${{ number_format($tier->min_amount, 0) }}+</span>
                    </h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
                        @foreach($tier->sponsors as $sponsor)
                        <div class="card-bento text-center p-6">
                            @if($sponsor->logo)
                                <img src="{{ Storage::url($sponsor->logo) }}" alt="{{ $sponsor->name }}"
                                     class="mx-auto mb-3 object-contain {{ $tier->logo_size === 'xl' ? 'h-20' : ($tier->logo_size === 'lg' ? 'h-16' : ($tier->logo_size === 'md' ? 'h-12' : 'h-8')) }}">
                            @endif
                            <h3 class="font-semibold text-sm" style="color: var(--on-surface);">{{ $sponsor->name }}</h3>
                            @if($sponsor->website_url)
                            <a href="{{ $sponsor->website_url }}" target="_blank" rel="noopener" class="text-xs mt-1 inline-block" style="color: var(--color-forest);">Visit Website</a>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            @endforeach

            {{-- Become a Sponsor CTA --}}
            <div class="card-bento p-8 text-center mt-8" style="background: linear-gradient(135deg, rgba(45,80,22,0.05), rgba(139,105,20,0.05));">
                <h3 class="font-display text-2xl font-bold mb-3" style="color: var(--on-surface);">Become a Sponsor</h3>
                <p class="mb-6" style="color: var(--on-surface-muted);">
                    Sponsor a hole, support the course, and get your name on permanent signage at Hebb Park.
                </p>
                <a href="{{ route('donate.index') }}" class="btn-donate">Contact Us About Sponsorship</a>
            </div>
        </div>
    </section>
</x-app-layout>
