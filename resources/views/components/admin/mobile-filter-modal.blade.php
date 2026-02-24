@props(['formAction', 'formId' => 'mobile-filter-form'])

<div x-data="{ filterOpen: false }" x-cloak class="md:hidden">
    <!-- Floating Action Button -->
    <div class="fixed bottom-6 right-6 z-40">
        <button @click="filterOpen = true"
                type="button"
                aria-label="Open filters"
                class="bg-admin-teal text-white rounded-full w-14 h-14 shadow-lg hover:bg-teal-700 transition-colors duration-200">
            <i class="fas fa-filter text-xl"></i>
        </button>
    </div>

    <!-- Overlay -->
    <div x-show="filterOpen"
         @click="filterOpen = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 bg-gray-900/50 z-40"
         style="display: none;"></div>

    <!-- Bottom Sheet -->
    <div x-show="filterOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         @keydown.escape.window="filterOpen = false"
         class="fixed bottom-0 left-0 right-0 bg-white rounded-t-2xl shadow-2xl z-50 max-h-[85vh] overflow-y-auto"
         style="display: none;">

        <!-- Header -->
        <div class="sticky top-0 bg-white border-b px-6 py-4 rounded-t-2xl z-10">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Filters</h3>
                <button @click="filterOpen = false"
                        type="button"
                        aria-label="Close filters"
                        class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Form Content -->
        <form method="GET" action="{{ $formAction }}" id="{{ $formId }}" class="p-6 space-y-4">
            {{ $slot }}

            <div class="flex gap-3 pt-4 border-t">
                <button type="submit" class="flex-1 btn-admin-primary">
                    <i class="fas fa-filter mr-2"></i>Apply
                </button>
                <a href="{{ $formAction }}" class="flex-1 btn-admin-secondary text-center">
                    <i class="fas fa-redo mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>
</div>
