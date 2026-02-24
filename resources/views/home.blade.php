<x-app-layout>
    @section('title', 'Help Build a Disc Golf Course at Hebb Park')

    {{-- Hero Section --}}
    <section class="relative min-h-[70vh] flex items-center justify-center bg-gradient-hero overflow-hidden">
        <div class="absolute inset-0 bg-black/40"></div>
        <div class="relative z-10 text-center px-4 max-w-4xl mx-auto">
            <h1 class="font-display text-white text-fluid-hero font-bold uppercase tracking-tight mb-6">
                {{ config('business.homepage.hero_heading') }}
            </h1>
            <p class="text-white/90 text-fluid-lg mb-8 max-w-2xl mx-auto">
                {{ config('business.homepage.hero_subheading') }}
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('donate.index') }}" class="btn-donate btn-lg">
                    <i class="fas fa-heart mr-2"></i> Donate Now
                </a>
                <a href="{{ route('products.index') }}" class="btn-glass btn-lg" style="color: white; border-color: rgba(255,255,255,0.3);">
                    <i class="fas fa-shopping-bag mr-2"></i> Shop Merch
                </a>
            </div>
        </div>
    </section>

    {{-- Progress Bar Section --}}
    <section class="py-12 px-4" style="background-color: var(--surface);">
        <div class="max-w-4xl mx-auto">
            <x-progress-bar :data="$progressData" />
        </div>
    </section>

    {{-- Mission Statement --}}
    <section class="py-16 px-4" style="background-color: var(--surface-raised);">
        <div class="max-w-3xl mx-auto text-center">
            <h2 class="home-section-heading text-gradient-nature mb-6">Our Mission</h2>
            <p class="text-fluid-lg" style="color: var(--on-surface-muted);">
                Hebb County Park sits along the Willamette River in West Linn, Oregon — a beautiful
                park with trails, a boat ramp, and stunning natural scenery. We're raising $15,000 to
                build a community disc golf course with 18 quality baskets, natural tee pads, and
                thoughtful course design that works with the landscape.
            </p>
        </div>
    </section>

    {{-- Featured Merch --}}
    @if($featuredProducts->isNotEmpty())
    <section class="py-16 px-4" style="background-color: var(--surface);">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="home-section-heading mb-4">Support the Cause</h2>
                <p style="color: var(--on-surface-muted);" class="text-fluid-base">Every purchase helps fund the course. 100% of profits go to Chains for Hebb.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($featuredProducts as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
            <div class="text-center mt-8">
                <a href="{{ route('products.index') }}" class="btn-gradient">View All Merch</a>
            </div>
        </div>
    </section>
    @endif

    {{-- Upcoming Events --}}
    @if($upcomingEvents->isNotEmpty())
    <section class="py-16 px-4" style="background-color: var(--surface-raised);">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="home-section-heading mb-4">Upcoming Events</h2>
                <p style="color: var(--on-surface-muted);" class="text-fluid-base">Join us for work parties, fundraisers, and community meetups.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($upcomingEvents as $event)
                <div class="card-bento">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full text-white text-sm font-bold" style="background: var(--gradient-primary);">
                            {{ $event->starts_at->format('d') }}
                        </span>
                        <div>
                            <div class="text-sm font-semibold" style="color: var(--on-surface);">{{ $event->starts_at->format('M Y') }}</div>
                            <div class="text-xs" style="color: var(--on-surface-muted);">{{ ucfirst(str_replace('_', ' ', $event->event_type)) }}</div>
                        </div>
                    </div>
                    <h3 class="font-display text-xl font-bold mb-2" style="color: var(--on-surface);">{{ $event->title }}</h3>
                    <p class="text-sm mb-4 line-clamp-2" style="color: var(--on-surface-muted);">{{ $event->description }}</p>
                    <a href="{{ route('events.show', $event) }}" class="btn-gradient btn-sm">RSVP</a>
                </div>
                @endforeach
            </div>
            <div class="text-center mt-8">
                <a href="{{ route('events.index') }}" class="btn-glass">View All Events</a>
            </div>
        </div>
    </section>
    @endif

    {{-- Sponsors --}}
    @if($featuredSponsors->isNotEmpty())
    <section class="py-16 px-4" style="background-color: var(--surface);">
        <div class="max-w-7xl mx-auto text-center">
            <h2 class="home-section-heading mb-8">Our Sponsors</h2>
            <div class="flex flex-wrap justify-center items-center gap-8">
                @foreach($featuredSponsors as $sponsor)
                <div class="flex items-center justify-center">
                    @if($sponsor->logo)
                        <img src="{{ Storage::url($sponsor->logo) }}" alt="{{ $sponsor->name }}" class="h-12 object-contain opacity-70 hover:opacity-100 transition-opacity">
                    @else
                        <span class="text-lg font-semibold" style="color: var(--on-surface-muted);">{{ $sponsor->name }}</span>
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
    <section class="py-16 px-4" style="background-color: var(--surface-raised);">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="home-section-heading mb-4">From the Park</h2>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($featuredPhotos as $photo)
                <div class="rounded-xl overflow-hidden aspect-square">
                    <img src="{{ Storage::url($photo->file_path) }}" alt="{{ $photo->alt_text }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                </div>
                @endforeach
            </div>
            <div class="text-center mt-8">
                <a href="{{ route('gallery.index') }}" class="btn-glass">View Gallery</a>
            </div>
        </div>
    </section>
    @endif

    {{-- Latest Blog Posts --}}
    @if($latestPosts->isNotEmpty())
    <section class="py-16 px-4" style="background-color: var(--surface);">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="home-section-heading mb-4">Latest Updates</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
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
            <div class="text-center mt-8">
                <a href="{{ route('blog.index') }}" class="btn-glass">All Updates</a>
            </div>
        </div>
    </section>
    @endif

    {{-- Donate CTA Banner --}}
    <section class="py-20 px-4 relative overflow-hidden" style="background: linear-gradient(135deg, #2D5016, #1A1A2E);">
        <div class="relative z-10 max-w-3xl mx-auto text-center">
            <h2 class="font-display text-white text-fluid-3xl font-bold uppercase mb-4">Every Dollar Counts</h2>
            <p class="text-white/80 text-fluid-base mb-8">
                Whether it's $10 or $1,000, your donation helps bring disc golf to Hebb Park.
                Join {{ \App\Models\Donation::paid()->count() }} supporters who are making this happen.
            </p>
            <a href="{{ route('donate.index') }}" class="btn-donate btn-lg">
                <i class="fas fa-heart mr-2"></i> Donate Now
            </a>
        </div>
    </section>

    {{-- Newsletter Signup --}}
    <section class="py-16 px-4" style="background-color: var(--surface);">
        <div class="max-w-xl mx-auto text-center">
            <h2 class="font-display text-2xl font-bold mb-4" style="color: var(--on-surface);">Stay in the Loop</h2>
            <p class="mb-6" style="color: var(--on-surface-muted);">Get updates on fundraising progress, events, and course construction.</p>
            <form action="{{ route('newsletter.subscribe') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                @csrf
                <input type="email" name="email" placeholder="Your email address" required class="input-glass flex-1">
                <button type="submit" class="btn-gradient whitespace-nowrap">Subscribe</button>
            </form>
        </div>
    </section>
</x-app-layout>
