@props(['category', 'type', 'depth'])

<div x-data="{ expanded: false }" class="space-y-1">
    <div class="flex items-center justify-between">
        <a href="{{ route($type . '.category', $category->slug) }}"
           class="flex-1 block text-white hover:text-gray-300 py-2 text-sm"
           style="padding-left: {{ $depth * 1 }}rem;">

            @if($depth > 0)
                <span class="text-gray-400 mr-1">└─</span>
            @endif

            {{ $category->name }}

            @if($type === 'products')
                <span class="ml-2 text-xs text-gray-400">
                    ({{ $category->active_product_count }})
                </span>
            @else
                <span class="ml-2 text-xs text-gray-400">
                    ({{ $category->active_service_count }})
                </span>
            @endif
        </a>

        @if($category->childrenRecursive && $category->childrenRecursive->isNotEmpty())
            <button @click="expanded = !expanded"
                    class="px-2 py-1 text-white"
                    aria-label="Toggle {{ $category->name }} subcategories">
                <i class="fas fa-chevron-down text-xs transition-transform duration-200"
                   :class="expanded ? 'rotate-180' : ''"></i>
            </button>
        @endif
    </div>

    {{-- Nested children --}}
    @if($category->childrenRecursive && $category->childrenRecursive->isNotEmpty())
        <div x-show="expanded"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform -translate-y-1"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             class="space-y-1 pl-3 border-l border-gray-600 ml-2">

            @foreach($category->childrenRecursive as $child)
                <x-mobile-category-menu-item :category="$child" :type="$type" :depth="$depth + 1" />
            @endforeach
        </div>
    @endif
</div>
