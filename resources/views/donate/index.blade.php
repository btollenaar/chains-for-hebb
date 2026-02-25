<x-app-layout>
    @section('title', 'Donate — Help Build Disc Golf at Hebb Park')

    {{-- Hero Banner --}}
    <section class="relative py-24 px-4 overflow-hidden" style="margin-top: -72px; padding-top: calc(72px + 4rem); background: linear-gradient(135deg, #2D5016, #1A1A2E);">
        @if(file_exists(public_path('images/generated/donate-hero-basket.webp')))
        <div class="absolute inset-0" style="background-image: url('{{ asset('images/generated/donate-hero-basket.webp') }}'); background-size: cover; background-position: center; opacity: 0.3;"></div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-[var(--surface)]"></div>
        <div class="relative z-10 max-w-4xl mx-auto text-center">
            <h1 class="font-display text-white text-fluid-4xl font-bold uppercase tracking-tight mb-4">Support Chains for Hebb</h1>
            <p class="text-white/80 text-fluid-base max-w-2xl mx-auto mb-6">
                Every donation brings us closer to building an 18-hole disc golf course at Hebb County Park.
            </p>
            {{-- Progress bar inline --}}
            @if(isset($progressData))
            <div class="max-w-md mx-auto">
                <div class="flex items-center justify-between text-sm text-white/80 mb-2">
                    <span class="font-bold text-white">${{ number_format($progressData['total_raised'] ?? 0, 0) }}</span>
                    <span>of ${{ number_format($progressData['goal'] ?? 15000, 0) }}</span>
                </div>
                <div class="h-2 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.2);">
                    <div class="h-full rounded-full" style="width: {{ $progressData['percentage'] ?? 0 }}%; background: linear-gradient(90deg, #2D8B46, #8B6914);"></div>
                </div>
            </div>
            @endif
        </div>
    </section>

    <section class="py-16 px-4" style="background-color: var(--surface);">
        <div class="max-w-4xl mx-auto">
            {{-- Donation Tiers --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12" data-animate="stagger">
                @foreach($tiers as $tier)
                <div class="card-bento text-center cursor-pointer hover:shadow-glass-lg transition-all relative overflow-hidden group"
                     x-data
                     @click="document.getElementById('amount').value = {{ $tier->suggested_amount }}; document.getElementById('tier_id').value = {{ $tier->id }};">
                    <div class="absolute top-0 left-0 right-0 h-1 opacity-0 group-hover:opacity-100 transition-opacity" style="background: var(--gradient-accent);"></div>
                    @if($tier->badge_icon)
                        <div class="text-4xl mb-3">{{ $tier->badge_icon }}</div>
                    @endif
                    <h3 class="font-display text-lg font-bold mb-1" style="color: var(--on-surface);">{{ $tier->name }}</h3>
                    <div class="text-3xl font-bold mb-2" style="color: var(--color-forest);">${{ number_format($tier->suggested_amount, 0) }}</div>
                    @if($tier->description)
                        <p class="text-sm" style="color: var(--on-surface-muted);">{{ $tier->description }}</p>
                    @endif
                </div>
                @endforeach
            </div>

            {{-- Donation Form --}}
            <div class="card-bento max-w-lg mx-auto p-8" data-animate="fade-up">
                <h2 class="font-display text-xl font-bold mb-6 text-center" style="color: var(--on-surface);">Make Your Donation</h2>

                {{-- Quick amount buttons --}}
                <div class="grid grid-cols-4 gap-2 mb-6" x-data>
                    @foreach([10, 25, 50, 100] as $amt)
                    <button type="button"
                            @click="document.getElementById('amount').value = {{ $amt }}"
                            class="py-2 rounded-lg font-semibold text-sm border transition-colors hover:border-[var(--color-forest)] hover:text-[var(--color-forest)]"
                            style="border-color: var(--surface-border); color: var(--on-surface);">
                        ${{ $amt }}
                    </button>
                    @endforeach
                </div>

                <form action="{{ route('donate.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <input type="hidden" name="tier_id" id="tier_id" value="">

                    <div>
                        <label for="amount" class="block text-sm font-medium mb-1" style="color: var(--on-surface);">Amount ($)</label>
                        <input type="number" name="amount" id="amount" min="1" step="0.01" required
                               class="input-glass" placeholder="25.00" value="{{ old('amount') }}">
                        @error('amount') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="donor_name" class="block text-sm font-medium mb-1" style="color: var(--on-surface);">Your Name</label>
                            <input type="text" name="donor_name" id="donor_name" required
                                   class="input-glass" value="{{ old('donor_name', auth()->user()?->name) }}">
                        </div>
                        <div>
                            <label for="donor_email" class="block text-sm font-medium mb-1" style="color: var(--on-surface);">Email</label>
                            <input type="email" name="donor_email" id="donor_email" required
                                   class="input-glass" value="{{ old('donor_email', auth()->user()?->email) }}">
                        </div>
                    </div>

                    <div>
                        <label for="donor_message" class="block text-sm font-medium mb-1" style="color: var(--on-surface);">Message (optional)</label>
                        <textarea name="donor_message" id="donor_message" rows="2" class="input-glass" placeholder="Leave a message of support...">{{ old('donor_message') }}</textarea>
                    </div>

                    <div>
                        <label for="display_name" class="block text-sm font-medium mb-1" style="color: var(--on-surface);">Display Name (for donor wall)</label>
                        <input type="text" name="display_name" id="display_name" class="input-glass"
                               placeholder="How you'd like to appear on the donor wall" value="{{ old('display_name') }}">
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_anonymous" id="is_anonymous" value="1" class="rounded border-gray-300">
                        <label for="is_anonymous" class="text-sm" style="color: var(--on-surface-muted);">Make my donation anonymous</label>
                    </div>

                    <input type="hidden" name="donation_type" value="one_time">

                    <button type="submit" class="btn-donate w-full btn-lg">
                        <i class="fas fa-heart mr-2"></i> Donate via Stripe
                    </button>
                </form>

                <p class="text-xs text-center mt-4" style="color: var(--on-surface-muted);">
                    Secure payment processed by Stripe. You'll receive a tax receipt via email.
                </p>
            </div>
        </div>
    </section>

    {{-- Donor Wall Preview --}}
    @if($donorWall->isNotEmpty())
    <section class="py-16 px-4 section-elevated">
        <div class="max-w-4xl mx-auto">
            <h2 class="home-section-heading text-center mb-8" data-animate="fade-up">Our Supporters</h2>
            <div class="flex flex-wrap justify-center gap-3" data-animate="stagger">
                @foreach($donorWall as $donor)
                <div class="badge-glass">
                    {{ $donor->display_name_attribute ?? $donor->donor_name }}
                    @if(!$donor->is_anonymous)
                     — ${{ number_format($donor->amount, 0) }}
                    @endif
                </div>
                @endforeach
            </div>
            <div class="text-center mt-6">
                <a href="{{ route('donate.wall') }}" class="btn-glass btn-sm">View Full Donor Wall</a>
            </div>
        </div>
    </section>
    @endif
</x-app-layout>
