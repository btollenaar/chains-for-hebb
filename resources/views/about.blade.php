<x-app-layout>
    @section('title', 'About Us')
    @section('meta_description', 'Learn about ' . config('business.profile.name', config('app.name')) . ' and the team behind every order.')

    <div class="py-12 md:py-16" style="background-color: var(--surface);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Page Header --}}
            <div class="text-center mb-10" data-animate="fade-up">
                <p class="text-sm font-semibold uppercase tracking-wider text-gradient mb-3">Our Story</p>
                <h1 class="text-fluid-3xl font-display font-bold mb-3" style="color: var(--on-surface);">About Us</h1>
                <p class="text-lg max-w-2xl mx-auto" style="color: var(--on-surface-muted);">Meet the team behind {{ config('business.profile.name', config('app.name')) }}</p>
            </div>

            {{-- About Content --}}
            <div class="max-w-5xl mx-auto">
                @if ($about && $about->published)
                    <div class="card-glass rounded-2xl p-6 md:p-8 mb-12" data-animate="fade-up">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12 items-start">
                            {{-- Image --}}
                            <div class="md:col-span-1">
                                @if ($about->image)
                                    <img src="{{ asset('storage/' . $about->image) }}" alt="{{ $about->name }}"
                                        class="w-full h-auto rounded-xl object-cover" loading="lazy">
                                @else
                                    <div class="w-full aspect-square rounded-xl flex items-center justify-center bg-gradient-to-br from-earth-primary/20 to-earth-green/20">
                                        <i class="fas fa-user text-6xl text-earth-primary/30"></i>
                                    </div>
                                @endif
                            </div>

                            {{-- Bio Content --}}
                            <div class="md:col-span-2">
                                <h2 class="text-fluid-2xl font-display font-bold mb-2" style="color: var(--on-surface);">
                                    {{ $about->name }}
                                </h2>
                                @if ($about->credentials)
                                    <div class="text-lg font-semibold mb-6 text-gradient">
                                        {!! $about->credentials !!}
                                    </div>
                                @endif

                                @if ($about->short_bio)
                                    <div class="text-lg leading-relaxed mb-6" style="color: var(--on-surface-muted);">
                                        {!! $about->short_bio !!}
                                    </div>
                                @endif

                                @if ($about->bio)
                                    <div class="prose prose-lg max-w-none mb-8" style="color: var(--on-surface-muted);">
                                        {!! $about->bio !!}
                                    </div>
                                @endif

                                {{-- CTA Buttons --}}
                                <div class="flex flex-col sm:flex-row gap-3 mt-8">
                                    <a href="{{ route('products.index') }}" class="btn-gradient btn-lg text-center">
                                        <i class="fas fa-store mr-2"></i>Shop Products
                                    </a>
                                    <a href="mailto:{{ $contactSettings['email'] }}" class="btn-glass btn-lg text-center" style="color: var(--on-surface);">
                                        <i class="fas fa-envelope mr-2"></i>Email Us
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Contact Info --}}
                    <div class="card-glass rounded-2xl p-6 md:p-8" data-animate="fade-up">
                        <div class="text-center mb-6">
                            <p class="text-sm font-semibold uppercase tracking-wider text-gradient mb-2">Reach Out</p>
                            <h2 class="text-fluid-xl font-display font-bold" style="color: var(--on-surface);">Get in Touch</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="text-center">
                                <div class="w-12 h-12 rounded-xl bg-earth-primary/10 flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-phone text-earth-primary"></i>
                                </div>
                                <p class="text-sm font-semibold mb-1" style="color: var(--on-surface);">Phone</p>
                                <a href="tel:{{ $contactSettings['phone'] }}" class="text-earth-primary hover:opacity-80 transition-opacity">
                                    {{ $contactSettings['phone'] }}
                                </a>
                            </div>
                            <div class="text-center">
                                <div class="w-12 h-12 rounded-xl bg-earth-green/10 flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-envelope text-earth-green"></i>
                                </div>
                                <p class="text-sm font-semibold mb-1" style="color: var(--on-surface);">Email</p>
                                <a href="mailto:{{ $contactSettings['email'] }}" class="text-earth-green hover:opacity-80 transition-opacity">
                                    {{ $contactSettings['email'] }}
                                </a>
                            </div>
                            <div class="text-center">
                                <div class="w-12 h-12 rounded-xl bg-earth-success/10 flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-map-marker-alt text-earth-success"></i>
                                </div>
                                <p class="text-sm font-semibold mb-1" style="color: var(--on-surface);">Address</p>
                                <p style="color: var(--on-surface-muted);">
                                    {{ $contactSettings['address']['street'] }}<br>
                                    {{ $contactSettings['address']['city'] }}, {{ $contactSettings['address']['state'] }} {{ $contactSettings['address']['zip'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-16" data-animate="fade-up">
                        <div class="w-20 h-20 rounded-2xl bg-earth-primary/10 flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-info-circle text-3xl text-earth-primary/50"></i>
                        </div>
                        <p class="text-lg font-display font-semibold" style="color: var(--on-surface);">About page coming soon...</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
