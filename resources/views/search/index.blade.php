<x-app-layout>
    @section('title', 'Search Results')
    @section('meta_description', 'Search results for "' . e($query) . '"')

    <div class="py-12 md:py-16" style="background-color: var(--surface);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Page Header --}}
            <div class="text-center mb-10" data-animate="fade-up">
                <p class="text-sm font-semibold uppercase tracking-wider text-gradient mb-3">Search</p>
                <h1 class="text-fluid-3xl font-display font-bold mb-3" style="color: var(--on-surface);">
                    Results for "<span class="text-gradient">{{ $query }}</span>"
                </h1>
            </div>

            {{-- Search Input --}}
            <div class="mb-10 max-w-lg mx-auto" data-animate="fade-up">
                <form method="GET" action="{{ route('search') }}">
                    <div class="relative">
                        <input type="text"
                               name="q"
                               value="{{ $query }}"
                               placeholder="Search products, blog..."
                               class="glass-input w-full pl-11 pr-4 py-3 rounded-xl text-sm"
                               style="color: var(--on-surface);"
                               minlength="2"
                               required>
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-search text-earth-primary/60"></i>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Type Filter Tabs --}}
            <div class="mb-8 overflow-x-auto scrollbar-hide" data-animate="fade-up">
                <div class="flex gap-2 pb-2 min-w-max justify-center">
                    <a href="{{ route('search', ['q' => $query]) }}"
                       class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 whitespace-nowrap {{ !$type ? 'bg-gradient-to-r from-earth-primary to-earth-green text-white shadow-md' : '' }}"
                       style="{{ !$type ? '' : 'background: var(--glass-bg); color: var(--on-surface); border: 1px solid var(--glass-border);' }}">
                        All
                        @if(!$type && is_array($results))
                            <span class="ml-1 opacity-75">({{ count($results['products']) + count($results['blog']) }})</span>
                        @endif
                    </a>
                    <a href="{{ route('search', ['q' => $query, 'type' => 'products']) }}"
                       class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 whitespace-nowrap {{ $type === 'products' ? 'bg-gradient-to-r from-earth-primary to-earth-green text-white shadow-md' : 'hover:bg-earth-primary/10' }}"
                       style="{{ $type === 'products' ? '' : 'background: var(--glass-bg); color: var(--on-surface); border: 1px solid var(--glass-border);' }}">
                        <i class="fas fa-box mr-1.5"></i>Products
                        @if($type === 'products' && $results instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            <span class="ml-1 opacity-75">({{ $results->total() }})</span>
                        @endif
                    </a>
                    <a href="{{ route('search', ['q' => $query, 'type' => 'blog']) }}"
                       class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 whitespace-nowrap {{ $type === 'blog' ? 'bg-gradient-to-r from-earth-primary to-earth-green text-white shadow-md' : 'hover:bg-earth-primary/10' }}"
                       style="{{ $type === 'blog' ? '' : 'background: var(--glass-bg); color: var(--on-surface); border: 1px solid var(--glass-border);' }}">
                        <i class="fas fa-newspaper mr-1.5"></i>Blog
                        @if($type === 'blog' && $results instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            <span class="ml-1 opacity-75">({{ $results->total() }})</span>
                        @endif
                    </a>
                </div>
            </div>

            {{-- Results --}}
            @if($type && $results instanceof \Illuminate\Pagination\LengthAwarePaginator)
                {{-- Paginated single-type results --}}
                @if($results->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" data-animate="stagger">
                        @foreach($results as $item)
                            @if($type === 'products')
                                <x-product-card :product="$item" />
                            @elseif($type === 'blog')
                                @include('search._blog-card', ['post' => $item])
                            @endif
                        @endforeach
                    </div>

                    <div class="mt-12">
                        {{ $results->links() }}
                    </div>
                @else
                    @include('search._empty-state')
                @endif

            @elseif(is_array($results))
                {{-- Combined results (all types) --}}
                @php
                    $totalResults = count($results['products']) + count($results['blog']);
                @endphp

                @if($totalResults === 0)
                    @include('search._empty-state')
                @else
                    {{-- Products Section --}}
                    @if(count($results['products']) > 0)
                        <div class="mb-12" data-animate="fade-up">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-fluid-xl font-display font-bold" style="color: var(--on-surface);">
                                    <i class="fas fa-box text-earth-primary mr-2"></i>Products
                                    <span class="text-sm font-normal ml-2" style="color: var(--on-surface-muted);">({{ count($results['products']) }})</span>
                                </h2>
                                @if(count($results['products']) > 4)
                                    <a href="{{ route('search', ['q' => $query, 'type' => 'products']) }}" class="text-sm font-semibold text-earth-primary hover:underline">
                                        View all <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                @endif
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" data-animate="stagger">
                                @foreach($results['products']->take(8) as $product)
                                    <x-product-card :product="$product" />
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Blog Section --}}
                    @if(count($results['blog']) > 0)
                        <div class="mb-12" data-animate="fade-up">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-fluid-xl font-display font-bold" style="color: var(--on-surface);">
                                    <i class="fas fa-newspaper text-earth-rose mr-2"></i>Blog Posts
                                    <span class="text-sm font-normal ml-2" style="color: var(--on-surface-muted);">({{ count($results['blog']) }})</span>
                                </h2>
                                @if(count($results['blog']) > 4)
                                    <a href="{{ route('search', ['q' => $query, 'type' => 'blog']) }}" class="text-sm font-semibold text-earth-primary hover:underline">
                                        View all <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                @endif
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" data-animate="stagger">
                                @foreach($results['blog']->take(6) as $post)
                                    @include('search._blog-card', ['post' => $post])
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif
            @else
                @include('search._empty-state')
            @endif
        </div>
    </div>
</x-app-layout>
