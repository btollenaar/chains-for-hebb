@foreach($posts as $post)
<article class="card-glass group overflow-hidden rounded-2xl transition-all duration-300 hover:-translate-y-1 hover:shadow-glass-lg">
    @if($post->featured_image)
    <a href="{{ route('blog.show', $post->slug) }}" class="block overflow-hidden">
        <img src="{{ asset('storage/' . $post->featured_image) }}"
             alt="{{ $post->featured_image_alt ?: $post->title }}"
             class="w-full h-52 object-cover transition-transform duration-500 group-hover:scale-105"
             loading="lazy">
    </a>
    @else
    <div class="w-full h-52 bg-gradient-to-br from-earth-primary/20 to-earth-green/20 flex items-center justify-center">
        <i class="fas fa-newspaper text-4xl text-earth-primary/40"></i>
    </div>
    @endif

    <div class="p-5">
        <div class="flex items-center text-sm mb-3 gap-2" style="color: var(--on-surface-muted);">
            <span class="inline-block px-3 py-0.5 bg-earth-primary/10 text-earth-primary rounded-full text-xs font-semibold">
                {{ $post->category->name }}
            </span>
            <span>&middot;</span>
            <time datetime="{{ $post->published_at->format('Y-m-d') }}">
                {{ $post->published_at->format('M d, Y') }}
            </time>
        </div>

        <h2 class="text-lg font-display font-bold mb-3 line-clamp-2" style="color: var(--on-surface);">
            <a href="{{ route('blog.show', $post->slug) }}" class="hover:text-earth-primary transition-colors">
                {{ $post->title }}
            </a>
        </h2>

        @if($post->excerpt)
        <div class="text-sm mb-4 line-clamp-2" style="color: var(--on-surface-muted);">
            {!! $post->excerpt !!}
        </div>
        @endif

        <div class="flex items-center justify-between">
            <span class="text-xs" style="color: var(--on-surface-muted);">
                By {{ $post->author->name ?? 'Unknown Author' }}
            </span>
            <a href="{{ route('blog.show', $post->slug) }}" class="text-earth-primary font-semibold text-sm flex items-center gap-1 group-hover:gap-2 transition-all">
                Read More <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
    </div>
</article>
@endforeach
