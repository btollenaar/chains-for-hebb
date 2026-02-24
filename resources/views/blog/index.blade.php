<x-app-layout>
    @section('title', 'Blog')
    @section('meta_description', 'Read our latest articles and insights.')

    <div class="py-12 md:py-16" style="background-color: var(--surface);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Page Header --}}
            <div class="text-center mb-10" data-animate="fade-up">
                <p class="text-sm font-semibold uppercase tracking-wider text-gradient mb-3">Insights</p>
                <h1 class="text-fluid-3xl font-display font-bold mb-3" style="color: var(--on-surface);">Our Blog</h1>
                <p class="text-lg max-w-2xl mx-auto" style="color: var(--on-surface-muted);">Stories, tips, and insights from our team</p>
            </div>

            {{-- Category Filter Pills --}}
            <div class="flex flex-wrap justify-center gap-2 mb-12" data-animate="fade-up">
                <a href="{{ route('blog.index') }}"
                   class="px-5 py-2 rounded-xl text-sm font-semibold transition-all duration-200 {{ !request('category') ? 'bg-gradient-to-r from-earth-primary to-earth-green text-white shadow-md' : '' }}"
                   style="{{ !request('category') ? '' : 'background: var(--glass-bg); color: var(--on-surface); border: 1px solid var(--glass-border);' }}">
                    All Posts
                </a>
                @foreach($categories as $category)
                <a href="{{ route('blog.index', ['category' => $category->slug]) }}"
                   class="px-5 py-2 rounded-xl text-sm font-semibold transition-all duration-200 {{ request('category') == $category->slug ? 'bg-gradient-to-r from-earth-primary to-earth-green text-white shadow-md' : 'hover:bg-earth-primary/10' }}"
                   style="{{ request('category') == $category->slug ? '' : 'background: var(--glass-bg); color: var(--on-surface); border: 1px solid var(--glass-border);' }}">
                    {{ $category->name }} ({{ $category->posts_count }})
                </a>
                @endforeach
            </div>

            {{-- Blog Posts Grid --}}
            @if($posts->count() > 0)
            <div id="posts-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8 mb-12" data-animate="stagger">
                @include('blog._posts')
            </div>

            <div id="posts-loading" class="text-center py-6 flex items-center justify-center gap-3">
                <div id="posts-spinner" class="w-6 h-6 border-4 border-earth-primary border-t-transparent rounded-full animate-spin hidden" aria-hidden="true"></div>
                <p id="posts-loading-text" style="color: var(--on-surface-muted);">Loading more posts…</p>
            </div>
            <div id="posts-sentinel"></div>
            @else
            <div class="text-center py-16" data-animate="fade-up">
                <div class="w-20 h-20 rounded-2xl bg-earth-primary/10 flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-inbox text-3xl text-earth-primary/50"></i>
                </div>
                <p class="text-lg font-display font-semibold mb-2" style="color: var(--on-surface);">No blog posts found</p>
                <p style="color: var(--on-surface-muted);">Check back soon for new articles.</p>
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        (function(){
            const postsGrid = document.getElementById('posts-grid');
            const sentinel = document.getElementById('posts-sentinel');
            const loadingText = document.getElementById('posts-loading-text');
            const spinner = document.getElementById('posts-spinner');

            let nextPage = {!! json_encode($posts->nextPageUrl()) !!};
            let loading = false;

            // Hide loading text initially if there are no more pages
            if (!nextPage && loadingText) {
                loadingText.textContent = '';
            }

            function appendHtml(html){
                const wrapper = document.createElement('div');
                wrapper.innerHTML = html;
                Array.from(wrapper.children).forEach(c => postsGrid.appendChild(c));
            }

            function showSpinner(){
                if (spinner) spinner.classList.remove('hidden');
                if (loadingText) loadingText.textContent = 'Loading more posts…';
            }

            function hideSpinner(){
                if (spinner) spinner.classList.add('hidden');
                if (loadingText) loadingText.textContent = '';
            }

            async function loadNext(){
                if (!nextPage || loading) return;
                loading = true;
                showSpinner();

                try {
                    const fetchUrl = nextPage;
                    const res = await fetch(fetchUrl, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    });
                    if (!res.ok) throw new Error('Network error');
                    const data = await res.json();
                    if (data.html) appendHtml(data.html);

                    try {
                        window.dataLayer = window.dataLayer || [];
                        const urlObj = new URL(fetchUrl, window.location.origin);
                        const loadedPage = urlObj.searchParams.get('page') || '2';
                        window.dataLayer.push({
                            event: 'blog_load_more',
                            loaded_page: loadedPage,
                            loaded_page_url: fetchUrl,
                            posts_loaded: postsGrid.querySelectorAll('article').length
                        });
                    } catch (e) {
                        // Analytics dataLayer push failed - non-critical, ignore
                    }

                    nextPage = data.next_page_url;
                    if (!nextPage) {
                        loadingText.textContent = 'No more posts.';
                        hideSpinner();
                        observer.disconnect();
                    } else {
                        hideSpinner();
                    }
                } catch (err){
                    // Infinite scroll load failed
                    loadingText.textContent = 'Failed to load more posts.';
                    hideSpinner();
                    observer.disconnect();
                }

                loading = false;
            }

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        loadNext();
                    }
                });
            }, { rootMargin: '400px' });

            if (sentinel) observer.observe(sentinel);
        })();
    </script>
    @endpush
</x-app-layout>
