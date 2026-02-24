<x-app-layout>
    @section('title', $page->meta_title ?? $page->title)
    @section('meta_description', $page->meta_description ?? $page->excerpt)

    <section class="py-16 px-4" style="background-color: var(--surface);">
        <div class="max-w-3xl mx-auto">
            @if($page->featured_image)
                <img src="{{ Storage::url($page->featured_image) }}" alt="{{ $page->title }}" class="w-full h-64 object-cover rounded-2xl mb-8">
            @endif

            <h1 class="page-heading mb-8" style="color: var(--on-surface);">{{ $page->title }}</h1>

            <div class="prose max-w-none" style="color: var(--on-surface);">
                {!! $page->content !!}
            </div>
        </div>
    </section>
</x-app-layout>
