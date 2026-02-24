<x-app-layout>
    @section('title', $page->meta_title ?? $page->title)

    <section class="py-16 px-4" style="background-color: var(--surface);">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-12">
                <h1 class="page-heading text-gradient-nature mb-4">{{ $page->title }}</h1>
                @if($page->excerpt)
                <p class="text-fluid-base" style="color: var(--on-surface-muted);">{{ $page->excerpt }}</p>
                @endif
            </div>

            @if($page->featured_image)
                <img src="{{ Storage::url($page->featured_image) }}" alt="{{ $page->title }}" class="w-full rounded-2xl mb-8 shadow-glass">
            @endif

            <div class="prose max-w-none" style="color: var(--on-surface);">
                {!! $page->content !!}
            </div>
        </div>
    </section>
</x-app-layout>
