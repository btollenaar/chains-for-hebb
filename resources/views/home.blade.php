<x-app-layout>
    @section('title', 'Help Build a Disc Golf Course at Hebb Park')

    {{-- Hero Section — Full Viewport --}}
    <section data-hero class="hero-nature" style="margin-top: -72px; @if(file_exists(public_path('images/generated/hero-hebb-park.webp')))background-image: url('{{ asset('images/generated/hero-hebb-park.webp') }}');@endif">
        {{-- Floating decorative elements --}}
        <svg data-float class="absolute top-[15%] left-[8%] w-16 h-16 opacity-20 z-[2]" viewBox="0 0 64 64" fill="none">
            <circle cx="32" cy="32" r="30" stroke="white" stroke-width="2" stroke-dasharray="8 4"/>
            <circle cx="32" cy="32" r="12" fill="white" opacity="0.3"/>
        </svg>
        <svg data-float class="absolute bottom-[25%] right-[10%] w-12 h-12 opacity-15 z-[2]" viewBox="0 0 48 48" fill="none">
            <path d="M24 4v40M4 24h40M10 10l28 28M38 10L10 38" stroke="white" stroke-width="1.5" opacity="0.5"/>
            <circle cx="24" cy="24" r="6" fill="white" opacity="0.3"/>
        </svg>

        <div class="relative z-10 text-center px-4 max-w-4xl mx-auto py-24">
            <div data-hero-label class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-sm font-semibold text-white/90 mb-6" style="background: rgba(45, 80, 22, 0.5); border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(8px);">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 2v4m0 12v4M2 12h4m12 0h4"/></svg>
                Chains for Hebb
            </div>

            <h1 data-hero-heading class="font-display text-white text-fluid-hero font-bold uppercase tracking-tight mb-6 leading-none">
                {{ config('business.homepage.hero_heading') }}
            </h1>

            <p data-hero-description class="text-white/90 text-fluid-lg mb-8 max-w-2xl mx-auto">
                {{ config('business.homepage.hero_subheading') }}
            </p>

            <div data-hero-cta class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('donate.index') }}" class="btn-donate btn-lg btn-donate-pulse">
                    <i class="fas fa-heart mr-2"></i> Donate Now
                </a>
                <a href="{{ route('products.index') }}" class="btn-glass btn-lg" style="color: white; border-color: rgba(255,255,255,0.3);">
                    <i class="fas fa-shopping-bag mr-2"></i> Shop Merch
                </a>
            </div>

            {{-- Inline progress social proof --}}
            @if(isset($progressData))
            <div data-hero-progress class="mt-10 max-w-md mx-auto">
                <div class="flex items-center justify-between text-sm text-white/80 mb-2">
                    <span class="font-bold text-white">${{ number_format($progressData['total_raised'] ?? 0, 0) }} raised</span>
                    <span>of ${{ number_format($progressData['goal'] ?? 15000, 0) }} goal</span>
                </div>
                <div class="h-2 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.2);">
                    <div class="h-full rounded-full" style="width: {{ $progressData['percentage'] ?? 0 }}%; background: linear-gradient(90deg, #2D8B46, #8B6914);"></div>
                </div>
                <p class="text-sm text-white/60 mt-2">
                    {{ \App\Models\Donation::paid()->count() }} supporters and counting
                </p>
            </div>
            @endif
        </div>

        {{-- Scroll indicator --}}
        <div data-hero-scroll class="absolute bottom-8 left-1/2 -translate-x-1/2 z-10">
            <div class="scroll-indicator flex flex-col items-center gap-1 text-white/50">
                <span class="text-xs uppercase tracking-widest">Scroll</span>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
            </div>
        </div>
    </section>

    {{-- Progress Bar Section --}}
    <section class="py-12 px-4 section-nature" data-animate="fade-up">
        <div class="max-w-4xl mx-auto">
            <x-progress-bar :data="$progressData" />
        </div>
    </section>

    {{-- Mission Statement --}}
    <section class="py-16 px-4 section-elevated">
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div data-animate="fade-up">
                    <h2 class="home-section-heading text-gradient-nature mb-6">Our Mission</h2>
                    <p class="text-fluid-lg mb-6" style="color: var(--on-surface-muted);">
                        Hebb County Park sits along the Willamette River in West Linn, Oregon — a beautiful
                        park with trails, a boat ramp, and stunning natural scenery.
                    </p>
                    <p class="text-fluid-base" style="color: var(--on-surface-muted);">
                        We're raising $15,000 to build a community disc golf course with 18 quality baskets,
                        natural tee pads, and thoughtful course design that works with the landscape.
                    </p>
                    <div class="mt-8">
                        <a href="{{ route('progress.index') }}" class="btn-outline-gradient">See Our Progress</a>
                    </div>
                </div>
                <div data-animate="scale-in" class="relative">
                    @if(file_exists(public_path('images/generated/mission-aerial.webp')))
                        <img src="{{ asset('images/generated/mission-aerial.webp') }}" alt="Hebb Park aerial view" class="rounded-2xl shadow-glass-lg w-full">
                    @else
                        <div class="aspect-[4/3] rounded-2xl overflow-hidden" style="background: var(--gradient-nature);">
                            <div class="w-full h-full flex flex-col items-center justify-center text-white/80 p-8">
                                <svg class="w-16 h-16 mb-4 opacity-60" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5a1.5 1.5 0 001.5-1.5V5.25a1.5 1.5 0 00-1.5-1.5H3.75a1.5 1.5 0 00-1.5 1.5v14.25a1.5 1.5 0 001.5 1.5z"/>
                                </svg>
                                <span class="font-display text-lg font-bold">Hebb Park, West Linn</span>
                                <span class="text-sm opacity-70 mt-1">Along the Willamette River</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Featured Merch --}}
    @if($featuredProducts->isNotEmpty())
    <section class="py-16 px-4 section-nature">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12" data-animate="fade-up">
                <h2 class="home-section-heading mb-4">Support the Cause</h2>
                <p style="color: var(--on-surface-muted);" class="text-fluid-base">Every purchase helps fund the course. 100% of profits go to Chains for Hebb.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8" data-animate="stagger">
                @foreach($featuredProducts as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
            <div class="text-center mt-8" data-animate="fade-up">
                <a href="{{ route('products.index') }}" class="btn-gradient">View All Merch</a>
            </div>
        </div>
    </section>
    @endif

    {{-- Upcoming Events --}}
    @if($upcomingEvents->isNotEmpty())
    <section class="py-16 px-4 section-elevated">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12" data-animate="fade-up">
                <h2 class="home-section-heading mb-4">Upcoming Events</h2>
                <p style="color: var(--on-surface-muted);" class="text-fluid-base">Join us for work parties, fundraisers, and community meetups.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8" data-animate="stagger">
                @foreach($upcomingEvents as $event)
                <div class="card-bento">
                    {{-- Date block --}}
                    <div class="flex items-center gap-3 mb-3">
                        <div class="flex-shrink-0 w-14 h-14 rounded-xl flex flex-col items-center justify-center text-white" style="background: var(--gradient-primary);">
                            <span class="text-lg font-bold leading-none">{{ $event->starts_at->format('d') }}</span>
                            <span class="text-[10px] uppercase tracking-wide">{{ $event->starts_at->format('M') }}</span>
                        </div>
                        <div>
                            <h3 class="font-display text-lg font-bold leading-tight" style="color: var(--on-surface);">{{ $event->title }}</h3>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wide mt-1 badge-event-{{ $event->event_type }}">
                                {{ ucfirst(str_replace('_', ' ', $event->event_type)) }}
                            </span>
                        </div>
                    </div>
                    <p class="text-sm mb-4 line-clamp-2" style="color: var(--on-surface-muted);">{{ $event->description }}</p>
                    <div class="flex items-center justify-between">
                        <a href="{{ route('events.show', $event) }}" class="btn-gradient btn-sm">RSVP</a>
                        @if($event->rsvps_count ?? false)
                        <span class="text-xs" style="color: var(--on-surface-muted);">
                            <i class="fas fa-users mr-1"></i>{{ $event->rsvps_count }} attending
                        </span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            <div class="text-center mt-8" data-animate="fade-up">
                <a href="{{ route('events.index') }}" class="btn-glass">View All Events</a>
            </div>
        </div>
    </section>
    @endif

    {{-- Sponsors --}}
    @if($featuredSponsors->isNotEmpty())
    <section class="py-16 px-4 section-nature">
        <div class="max-w-7xl mx-auto text-center" data-animate="fade-up">
            <h2 class="home-section-heading mb-2">Our Sponsors</h2>
            <p class="text-fluid-base mb-8" style="color: var(--on-surface-muted);">Backed by local businesses who love the outdoors.</p>
            <div class="flex flex-wrap justify-center items-center gap-8" data-animate="stagger">
                @foreach($featuredSponsors as $sponsor)
                <div class="flex items-center justify-center card-bento px-6 py-4">
                    @if($sponsor->logo)
                        <img src="{{ Storage::url($sponsor->logo) }}" alt="{{ $sponsor->name }}" class="h-12 object-contain opacity-70 hover:opacity-100 transition-opacity">
                    @else
                        <span class="text-lg font-display font-bold" style="color: var(--on-surface-muted);">{{ $sponsor->name }}</span>
                    @endif
                </div>
                @endforeach
            </div>
            <div class="mt-8">
                <a href="{{ route('sponsors.index') }}" class="btn-glass btn-sm">Become a Sponsor</a>
            </div>
        </div>
    </section>
    @endif

    {{-- Gallery Preview --}}
    @if($featuredPhotos->isNotEmpty())
    <section class="py-16 px-4 section-elevated">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12" data-animate="fade-up">
                <h2 class="home-section-heading mb-4">From the Park</h2>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4" data-animate="stagger">
                @foreach($featuredPhotos as $index => $photo)
                <div class="rounded-xl overflow-hidden {{ $index === 0 ? 'md:row-span-2 md:col-span-1' : '' }} {{ $index === 0 ? 'aspect-auto' : 'aspect-square' }} relative group">
                    <img src="{{ Storage::url($photo->file_path) }}" alt="{{ $photo->alt_text }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                        <span class="text-white text-sm font-semibold">{{ $photo->alt_text }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="text-center mt-8" data-animate="fade-up">
                <a href="{{ route('gallery.index') }}" class="btn-glass">View Gallery</a>
            </div>
        </div>
    </section>
    @endif

    {{-- Latest Blog Posts --}}
    @if($latestPosts->isNotEmpty())
    <section class="py-16 px-4 section-nature">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12" data-animate="fade-up">
                <h2 class="home-section-heading mb-4">Latest Updates</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8" data-animate="stagger">
                @foreach($latestPosts as $post)
                <a href="{{ route('blog.show', $post->slug) }}" class="card-bento block hover:shadow-glass transition-shadow">
                    <div class="text-xs font-semibold uppercase tracking-wide mb-2" style="color: var(--color-forest);">
                        {{ $post->published_at?->format('M d, Y') }}
                    </div>
                    <h3 class="font-display text-lg font-bold mb-2" style="color: var(--on-surface);">{{ $post->title }}</h3>
                    <p class="text-sm line-clamp-2" style="color: var(--on-surface-muted);">{{ $post->excerpt ?? Str::limit(strip_tags($post->content), 120) }}</p>
                </a>
                @endforeach
            </div>
            <div class="text-center mt-8" data-animate="fade-up">
                <a href="{{ route('blog.index') }}" class="btn-glass">All Updates</a>
            </div>
        </div>
    </section>
    @endif

    {{-- Donate CTA Banner --}}
    <section class="py-20 px-4 relative overflow-hidden section-dark" data-animate="fade-in">
        {{-- Subtle pattern overlay --}}
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 32px 32px;"></div>
        <div class="relative z-10 max-w-3xl mx-auto text-center">
            <h2 class="font-display text-white text-fluid-3xl font-bold uppercase mb-4" data-animate="fade-up">Every Dollar Counts</h2>
            <p class="text-white/80 text-fluid-base mb-8" data-animate="fade-up">
                Whether it's $10 or $1,000, your donation helps bring disc golf to Hebb Park.
                Join {{ \App\Models\Donation::paid()->count() }} supporters who are making this happen.
            </p>
            <a href="{{ route('donate.index') }}" class="btn-donate btn-lg btn-donate-pulse" data-animate="scale-in">
                <i class="fas fa-heart mr-2"></i> Donate Now
            </a>
        </div>
    </section>

    {{-- Newsletter Signup --}}
    <section class="py-16 px-4 relative overflow-hidden section-nature">
        {{-- Forest silhouette background --}}
        <div class="absolute bottom-0 left-0 right-0 h-32 opacity-5 z-0" style="background: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1200 120'%3E%3Cpath d='M0 120 L50 60 L80 90 L120 40 L160 80 L200 30 L240 70 L280 20 L320 60 L360 25 L400 65 L440 15 L480 55 L520 30 L560 70 L600 10 L640 50 L680 35 L720 75 L760 20 L800 60 L840 25 L880 65 L920 15 L960 55 L1000 30 L1040 70 L1080 20 L1120 60 L1160 35 L1200 50 L1200 120Z' fill='%232D5016'/%3E%3C/svg%3E\") repeat-x bottom center; background-size: auto 120px;"></div>
        <div class="relative z-10 max-w-xl mx-auto text-center" data-animate="fade-up">
            <h2 class="font-display text-2xl font-bold mb-4" style="color: var(--on-surface);">Stay in the Loop</h2>
            <p class="mb-6" style="color: var(--on-surface-muted);">Get updates on fundraising progress, events, and course construction.</p>
            <form action="{{ route('newsletter.subscribe') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                @csrf
                <input type="email" name="email" placeholder="Your email address" required class="input-glass flex-1">
                <button type="submit" class="btn-gradient whitespace-nowrap">Subscribe</button>
            </form>
        </div>
    </section>

    {{-- Sticky Mobile Donate CTA --}}
    <div x-data="{ show: false }"
         @scroll.window="show = window.scrollY > 600"
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="translate-y-full opacity-0"
         class="fixed bottom-0 inset-x-0 z-40 lg:hidden p-3 backdrop-blur-lg border-t"
         style="display: none; background: var(--surface-overlay); border-color: var(--surface-border);">
        <a href="{{ route('donate.index') }}" class="btn-donate w-full text-center btn-lg">
            <i class="fas fa-heart mr-2"></i> Donate Now
        </a>
    </div>

    @push('scripts')
        @vite('resources/js/pages/home.js')
    @endpush
</x-app-layout>
