<x-app-layout>
    @section('title', $page->meta_title ?? $page->title)

    <section class="py-16 px-4" style="background-color: var(--surface);">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-12">
                <h1 class="page-heading text-gradient-nature mb-4">{{ $page->title }}</h1>
            </div>

            {{-- Action Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-12">
                <div class="card-bento text-center p-8">
                    <div class="text-4xl mb-4">💰</div>
                    <h3 class="font-display text-xl font-bold mb-2" style="color: var(--on-surface);">Donate</h3>
                    <p class="text-sm mb-4" style="color: var(--on-surface-muted);">Every dollar counts toward building the course.</p>
                    <a href="{{ route('donate.index') }}" class="btn-donate btn-sm">Donate Now</a>
                </div>
                <div class="card-bento text-center p-8">
                    <div class="text-4xl mb-4">🛠️</div>
                    <h3 class="font-display text-xl font-bold mb-2" style="color: var(--on-surface);">Volunteer</h3>
                    <p class="text-sm mb-4" style="color: var(--on-surface-muted);">Join our work parties to help clear trails and build the course.</p>
                    <a href="{{ route('events.index') }}" class="btn-gradient btn-sm">See Events</a>
                </div>
                <div class="card-bento text-center p-8">
                    <div class="text-4xl mb-4">🏢</div>
                    <h3 class="font-display text-xl font-bold mb-2" style="color: var(--on-surface);">Sponsor</h3>
                    <p class="text-sm mb-4" style="color: var(--on-surface-muted);">Get your business name on course signage.</p>
                    <a href="{{ route('sponsors.index') }}" class="btn-gradient btn-sm">Learn More</a>
                </div>
                <div class="card-bento text-center p-8">
                    <div class="text-4xl mb-4">📢</div>
                    <h3 class="font-display text-xl font-bold mb-2" style="color: var(--on-surface);">Spread the Word</h3>
                    <p class="text-sm mb-4" style="color: var(--on-surface-muted);">Share our mission with friends, family, and social media.</p>
                    <a href="{{ route('products.index') }}" class="btn-gradient btn-sm">Shop & Share</a>
                </div>
            </div>

            @if($page->content)
            <div class="prose max-w-none" style="color: var(--on-surface);">
                {!! $page->content !!}
            </div>
            @endif
        </div>
    </section>
</x-app-layout>
