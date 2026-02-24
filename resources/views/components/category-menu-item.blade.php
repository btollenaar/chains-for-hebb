@props(['category', 'type', 'depth' => 0])

<div x-data="{ childrenOpen: false }"
     @if($category->childrenRecursive && $category->childrenRecursive->isNotEmpty())
         @click.away="childrenOpen = false"
     @endif
     class="relative">
    <div class="flex items-center justify-between">

        <a href="{{ route($type . '.category', $category->slug) }}"
           class="flex-1 block px-4 py-2 text-sm hover:bg-earth-primary/10 hover:text-earth-primary transition-colors"
           style="padding-left: {{ ($depth * 1) + 1 }}rem; color: var(--on-surface);">

            @if($depth > 0)
                <span style="color: var(--on-surface-muted);" class="mr-1">└─</span>
            @endif

            {{ $category->name }}

            @if($type === 'products')
                <span class="ml-2 text-xs" style="color: var(--on-surface-muted);">
                    ({{ $category->active_product_count }})
                </span>
            @else
                <span class="ml-2 text-xs" style="color: var(--on-surface-muted);">
                    ({{ $category->active_service_count }})
                </span>
            @endif
        </a>

        {{-- Chevron button for categories with children --}}
        @if($category->childrenRecursive && $category->childrenRecursive->isNotEmpty())
            <button @click.stop="childrenOpen = !childrenOpen"
                    type="button"
                    class="px-2 py-2 transition-colors"
                    style="color: var(--on-surface-muted);"
                    aria-label="Toggle {{ $category->name }} subcategories">
                <i class="fas fa-chevron-right text-xs transition-transform duration-200"
                   :class="childrenOpen ? 'rotate-90' : ''"></i>
            </button>
        @endif
    </div>

    {{-- Flyout submenu for children (appears to the right) --}}
    @if($category->childrenRecursive && $category->childrenRecursive->isNotEmpty())
        <div x-show="childrenOpen"
             x-transition
             class="absolute left-full top-0 ml-1 w-64 glass-card py-2 z-50"
             style="display: none;">

            @foreach($category->childrenRecursive as $child)
                <x-category-menu-item :category="$child" :type="$type" :depth="$depth + 1" />
            @endforeach
        </div>
    @endif
</div>
