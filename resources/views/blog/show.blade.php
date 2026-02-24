<x-app-layout>
    @section('title', $post->title)
    @section('meta_description', $post->excerpt ?? Str::limit(strip_tags($post->content), 160))
    @section('og_type', 'article')
    @section('og_title', $post->title)
    @section('og_description', $post->excerpt ?? Str::limit(strip_tags($post->content), 160))
    @if($post->featured_image)
        @section('og_image', asset('storage/' . $post->featured_image))
    @endif
    @section('canonical_url', route('blog.show', $post->slug))

    @push('head')
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'headline' => $post->title,
        'description' => $post->excerpt ?? Str::limit(strip_tags($post->content), 160),
        'url' => route('blog.show', $post->slug),
        'datePublished' => $post->created_at->toIso8601String(),
        'dateModified' => $post->updated_at->toIso8601String(),
        'author' => ['@type' => 'Organization', 'name' => config('app.name')],
        'publisher' => ['@type' => 'Organization', 'name' => config('app.name')],
        'image' => $post->featured_image ? asset('storage/' . $post->featured_image) : null,
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
    @endpush

    <article class="py-8 md:py-12" style="background-color: var(--surface);">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-sm mb-6" data-animate="fade-in">
                <a href="{{ route('home') }}" class="hover:text-earth-primary transition-colors" style="color: var(--on-surface-muted);">Home</a>
                <i class="fas fa-chevron-right text-[10px]" style="color: var(--on-surface-muted); opacity: 0.5;"></i>
                <a href="{{ route('blog.index') }}" class="hover:text-earth-primary transition-colors" style="color: var(--on-surface-muted);">Blog</a>
                <i class="fas fa-chevron-right text-[10px]" style="color: var(--on-surface-muted); opacity: 0.5;"></i>
                <span class="font-medium truncate" style="color: var(--on-surface);">{{ $post->title }}</span>
            </nav>

            {{-- Featured Image --}}
            @if($post->featured_image)
            <div class="mb-8 rounded-2xl overflow-hidden" data-animate="fade-up">
                <img src="{{ asset('storage/' . $post->featured_image) }}"
                     alt="{{ $post->title }}"
                     class="w-full object-cover max-h-[500px]">
            </div>
            @endif

            {{-- Post Header --}}
            <header class="mb-8" data-animate="fade-up">
                <div class="flex items-center text-sm mb-4 gap-2" style="color: var(--on-surface-muted);">
                    <a href="{{ route('blog.index', ['category' => $post->category->slug]) }}"
                       class="inline-block px-3 py-0.5 bg-earth-primary/10 text-earth-primary rounded-full text-xs font-semibold hover:bg-earth-primary/20 transition-colors">
                        {{ $post->category->name }}
                    </a>
                    <span>&middot;</span>
                    <time datetime="{{ $post->published_at->format('Y-m-d') }}">
                        {{ $post->published_at->format('F d, Y') }}
                    </time>
                    <span>&middot;</span>
                    <span>By {{ $post->author->name }}</span>
                </div>

                <h1 class="text-fluid-3xl font-display font-bold mb-6" style="color: var(--on-surface);">
                    {{ $post->title }}
                </h1>

                @if($post->excerpt)
                <div class="text-lg leading-relaxed" style="color: var(--on-surface-muted);">
                    {!! $post->excerpt !!}
                </div>
                @endif
            </header>

            {{-- Post Content --}}
            <div class="prose prose-lg max-w-none mb-12" style="color: var(--on-surface-muted);">
                {!! $post->content !!}
            </div>

            {{-- Share Buttons --}}
            <div class="py-6 mb-12" style="border-top: 1px solid var(--glass-border); border-bottom: 1px solid var(--glass-border);">
                <div class="flex items-center justify-between">
                    <span class="font-display font-semibold" style="color: var(--on-surface);">Share this article:</span>
                    <div class="flex gap-3">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('blog.show', $post->slug)) }}"
                           target="_blank" rel="noopener noreferrer"
                           class="w-9 h-9 rounded-lg flex items-center justify-center transition-colors hover:bg-[#1877F2]/10" style="background: var(--glass-bg);">
                            <i class="fab fa-facebook-f text-[#1877F2]"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('blog.show', $post->slug)) }}&text={{ urlencode($post->title) }}"
                           target="_blank" rel="noopener noreferrer"
                           class="w-9 h-9 rounded-lg flex items-center justify-center transition-colors hover:bg-gray-500/10" style="background: var(--glass-bg);">
                            <i class="fab fa-x-twitter" style="color: var(--on-surface);"></i>
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(route('blog.show', $post->slug)) }}&title={{ urlencode($post->title) }}"
                           target="_blank" rel="noopener noreferrer"
                           class="w-9 h-9 rounded-lg flex items-center justify-center transition-colors hover:bg-[#0A66C2]/10" style="background: var(--glass-bg);">
                            <i class="fab fa-linkedin-in text-[#0A66C2]"></i>
                        </a>
                        <a href="mailto:?subject={{ urlencode($post->title) }}&body={{ urlencode(route('blog.show', $post->slug)) }}"
                           class="w-9 h-9 rounded-lg flex items-center justify-center transition-colors hover:bg-earth-primary/10" style="background: var(--glass-bg);">
                            <i class="fas fa-envelope text-earth-primary"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </article>

    {{-- Related Posts --}}
    @if($relatedPosts->count() > 0)
    <section class="py-12 md:py-16" style="background-color: var(--surface-raised);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <p class="text-sm font-semibold uppercase tracking-wider text-gradient mb-2">Keep reading</p>
                <h2 class="text-fluid-2xl font-display font-bold" style="color: var(--on-surface);">Related Articles</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8" data-animate="stagger">
                @foreach($relatedPosts as $relatedPost)
                <article class="card-glass group overflow-hidden rounded-2xl transition-all duration-300 hover:-translate-y-1 hover:shadow-glass-lg">
                    @if($relatedPost->featured_image)
                    <a href="{{ route('blog.show', $relatedPost->slug) }}" class="block overflow-hidden">
                        <img src="{{ asset('storage/' . $relatedPost->featured_image) }}"
                             alt="{{ $relatedPost->title }}"
                             class="w-full h-48 object-cover transition-transform duration-500 group-hover:scale-105"
                             loading="lazy">
                    </a>
                    @else
                    <div class="w-full h-48 bg-gradient-to-br from-earth-primary/20 to-earth-green/20 flex items-center justify-center">
                        <i class="fas fa-newspaper text-4xl text-earth-primary/40"></i>
                    </div>
                    @endif

                    <div class="p-5">
                        <h3 class="text-lg font-display font-bold mb-2 line-clamp-2" style="color: var(--on-surface);">
                            <a href="{{ route('blog.show', $relatedPost->slug) }}" class="hover:text-earth-primary transition-colors">
                                {{ $relatedPost->title }}
                            </a>
                        </h3>
                        @if($relatedPost->excerpt)
                        <div class="text-sm line-clamp-2" style="color: var(--on-surface-muted);">
                            {!! $relatedPost->excerpt !!}
                        </div>
                        @endif
                    </div>
                </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif
</x-app-layout>
