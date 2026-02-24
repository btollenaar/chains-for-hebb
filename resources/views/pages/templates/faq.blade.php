<x-app-layout>
    @section('title', $page->meta_title ?? $page->title)

    <section class="py-16 px-4" style="background-color: var(--surface);">
        <div class="max-w-3xl mx-auto">
            <h1 class="page-heading text-gradient-nature text-center mb-12">{{ $page->title }}</h1>

            <div class="prose max-w-none" style="color: var(--on-surface);">
                {!! $page->content !!}
            </div>
        </div>
    </section>
</x-app-layout>
