<div class="card-glass group overflow-hidden rounded-2xl transition-all duration-300 hover:-translate-y-1 hover:shadow-glass-lg">
    {{-- Blog Image --}}
    <div class="relative overflow-hidden">
        <a href="{{ route('blog.show', $post->slug) }}">
            @if($post->featured_image)
                <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->featured_image_alt ?? $post->title }}" class="w-full h-48 object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
            @else
                <div class="w-full h-48 flex items-center justify-center" style="background: linear-gradient(135deg, var(--surface-raised), var(--surface));">
                    <i class="fas fa-newspaper text-4xl" style="color: var(--on-surface-muted);"></i>
                </div>
            @endif
        </a>

        @if($post->category)
            <span class="absolute top-3 left-3 badge-glass text-xs font-bold uppercase tracking-wider px-2.5 py-1">
                {{ $post->category->name }}
            </span>
        @endif
    </div>

    {{-- Blog Info --}}
    <div class="p-5">
        <h3 class="font-display font-bold text-base mb-2 line-clamp-2" style="color: var(--on-surface);">
            <a href="{{ route('blog.show', $post->slug) }}" class="hover:text-earth-primary transition-colors">
                {{ $post->title }}
            </a>
        </h3>

        @if($post->excerpt)
            <p class="text-sm mb-4 line-clamp-3" style="color: var(--on-surface-muted);">
                {{ Str::limit(strip_tags($post->excerpt), 120) }}
            </p>
        @endif

        <div class="flex items-center justify-between">
            @if($post->published_at)
                <span class="text-xs" style="color: var(--on-surface-muted);">
                    <i class="far fa-calendar-alt mr-1"></i>{{ $post->published_at->format('M j, Y') }}
                </span>
            @endif
            <a href="{{ route('blog.show', $post->slug) }}" class="text-sm font-semibold text-earth-primary hover:underline">
                Read More <i class="fas fa-arrow-right ml-1 text-xs"></i>
            </a>
        </div>
    </div>
</div>
