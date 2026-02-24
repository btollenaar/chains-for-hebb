@extends('layouts.admin')

@section('title', 'Add Printful Product')

@section('content')
<div class="p-4 md:p-6 lg:p-8" x-data="printfulSetup()">
    {{-- Breadcrumb --}}
    <div class="mb-4">
        <a href="{{ route('admin.printful.catalog') }}" class="text-sm text-admin-teal hover:underline">
            <i class="fas fa-arrow-left mr-1"></i>Back to Catalog
        </a>
    </div>

    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row gap-6 mb-8">
        {{-- Product Image --}}
        <div class="w-full md:w-48 flex-shrink-0">
            @if(!empty($product['image']))
                <img src="{{ $product['image'] }}" alt="{{ $product['title'] ?? $product['name'] ?? '' }}"
                    class="w-full rounded-lg shadow-sm">
            @else
                <div class="w-full aspect-square bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-image text-gray-300 text-4xl"></i>
                </div>
            @endif
        </div>
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ $product['title'] ?? $product['name'] ?? 'Printful Product' }}</h1>
            <p class="text-sm text-gray-500 mt-1">Printful Product #{{ $printfulProductId }}</p>
            @if(!empty($product['description']))
                <p class="text-sm text-gray-600 mt-2 line-clamp-3">{!! strip_tags($product['description']) !!}</p>
            @endif
        </div>
    </div>

    <form action="{{ route('admin.printful.store') }}" method="POST">
        @csrf
        <input type="hidden" name="printful_product_id" value="{{ $printfulProductId }}">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Column --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Step 1: Product Details --}}
                <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-admin-teal text-white text-sm mr-2">1</span>
                        Product Details
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Product Name *</label>
                            <input type="text" name="name" id="name"
                                value="{{ old('name', $product['title'] ?? $product['name'] ?? '') }}"
                                class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-2 focus:ring-admin-teal focus:border-admin-teal"
                                required>
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">URL Slug</label>
                            <input type="text" name="slug" id="slug"
                                value="{{ old('slug') }}"
                                placeholder="auto-generated-from-name"
                                class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-2 focus:ring-admin-teal focus:border-admin-teal">
                            @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" id="description" rows="4"
                                class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-2 focus:ring-admin-teal focus:border-admin-teal"
                            >{{ old('description', strip_tags($product['description'] ?? '')) }}</textarea>
                            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Step 2: Select Variants --}}
                <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-admin-teal text-white text-sm mr-2">2</span>
                        Select Variants
                    </h2>
                    <p class="text-sm text-gray-500 mb-4">Choose which size/color combinations to sell. Set your retail price for each variant.</p>

                    @error('variants') <p class="text-red-500 text-sm mb-3">{{ $message }}</p> @enderror

                    {{-- Quick Actions --}}
                    <div class="flex flex-wrap gap-2 mb-4">
                        <button type="button" @click="selectAllVariants()" class="btn-admin-secondary btn-admin-sm">
                            <i class="fas fa-check-double mr-1"></i>Select All
                        </button>
                        <button type="button" @click="deselectAllVariants()" class="btn-admin-secondary btn-admin-sm">
                            <i class="fas fa-times mr-1"></i>Deselect All
                        </button>
                        <div class="flex items-center gap-2 ml-auto">
                            <label class="text-sm text-gray-600">Markup %:</label>
                            <input type="number" x-model.number="defaultMarkup" min="0" max="500" step="5"
                                class="w-20 border border-gray-300 rounded-md px-2 py-1 text-sm">
                            <button type="button" @click="applyMarkupToAll()" class="btn-admin-primary btn-admin-sm">Apply</button>
                        </div>
                    </div>

                    {{-- Variant Table --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 text-left">
                                    <th class="pb-2 pl-2 w-8">
                                        <input type="checkbox" @change="toggleAllVariants($event.target.checked)"
                                            class="rounded border-gray-300 text-admin-teal focus:ring-admin-teal">
                                    </th>
                                    <th class="pb-2">Color</th>
                                    <th class="pb-2">Size</th>
                                    <th class="pb-2 text-right">Printful Cost</th>
                                    <th class="pb-2 text-right">Retail Price</th>
                                    <th class="pb-2 text-right">Profit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($variants as $index => $variant)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50"
                                        :class="selectedVariants[{{ $index }}] ? 'bg-blue-50' : ''">
                                        <td class="py-2 pl-2">
                                            <input type="checkbox"
                                                x-model="selectedVariants[{{ $index }}]"
                                                class="rounded border-gray-300 text-admin-teal focus:ring-admin-teal">
                                        </td>
                                        <td class="py-2">
                                            <div class="flex items-center gap-2">
                                                @if(!empty($variant['color_code']))
                                                    <span class="w-4 h-4 rounded-full border border-gray-200"
                                                        style="background-color: {{ $variant['color_code'] }}"></span>
                                                @endif
                                                {{ $variant['color'] ?? '—' }}
                                            </div>
                                        </td>
                                        <td class="py-2">{{ $variant['size'] ?? '—' }}</td>
                                        <td class="py-2 text-right font-mono">${{ number_format($variant['price'] ?? 0, 2) }}</td>
                                        <td class="py-2 text-right">
                                            <template x-if="selectedVariants[{{ $index }}]">
                                                <input type="number"
                                                    x-model.number="retailPrices[{{ $index }}]"
                                                    step="0.01" min="0"
                                                    class="w-24 border border-gray-300 rounded px-2 py-1 text-sm text-right focus:ring-2 focus:ring-admin-teal">
                                            </template>
                                            <template x-if="!selectedVariants[{{ $index }}]">
                                                <span class="text-gray-400">—</span>
                                            </template>
                                        </td>
                                        <td class="py-2 text-right">
                                            <template x-if="selectedVariants[{{ $index }}]">
                                                <span :class="(retailPrices[{{ $index }}] - {{ $variant['price'] ?? 0 }}) >= 0 ? 'text-green-600' : 'text-red-600'"
                                                    class="font-mono text-sm"
                                                    x-text="'$' + (retailPrices[{{ $index }}] - {{ $variant['price'] ?? 0 }}).toFixed(2)">
                                                </span>
                                            </template>
                                            <template x-if="!selectedVariants[{{ $index }}]">
                                                <span class="text-gray-400">—</span>
                                            </template>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Hidden inputs for selected variants (sparse array — controller filters unselected) --}}
                    <template x-for="(selected, index) in selectedVariants" :key="index">
                        <template x-if="selected">
                            <div>
                                <input type="hidden" :name="'variants[' + index + '][printful_variant_id]'"
                                    :value="variantData[index].id">
                                <input type="hidden" :name="'variants[' + index + '][color_name]'"
                                    :value="variantData[index].color">
                                <input type="hidden" :name="'variants[' + index + '][color_hex]'"
                                    :value="variantData[index].color_code">
                                <input type="hidden" :name="'variants[' + index + '][size]'"
                                    :value="variantData[index].size">
                                <input type="hidden" :name="'variants[' + index + '][printful_cost]'"
                                    :value="variantData[index].price">
                                <input type="hidden" :name="'variants[' + index + '][retail_price]'"
                                    :value="retailPrices[index]">
                            </div>
                        </template>
                    </template>

                    <div class="mt-4 p-3 bg-blue-50 rounded-lg text-sm text-blue-800">
                        <i class="fas fa-info-circle mr-1"></i>
                        <strong x-text="Object.values(selectedVariants).filter(Boolean).length">0</strong> variant(s) selected.
                        Designs and mockups can be uploaded after creating the product.
                    </div>
                </div>

                {{-- Step 3: Print Areas Reference --}}
                @if(!empty($printAreas))
                    <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-200 text-gray-600 text-sm mr-2">3</span>
                            Print Areas
                        </h2>
                        <p class="text-sm text-gray-500 mb-3">This product supports the following print placements. Upload designs after creating the product.</p>

                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($printAreas as $area)
                                <div class="border border-gray-200 rounded-lg p-3 text-center">
                                    <i class="fas fa-print text-gray-400 text-lg mb-1"></i>
                                    <p class="text-sm font-medium text-gray-700">{{ $area['title'] ?? $area['type'] ?? 'Print Area' }}</p>
                                    <p class="text-xs text-gray-400">{{ $area['type'] ?? '' }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Status & Visibility --}}
                <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
                    <h3 class="text-sm font-bold text-gray-900 mb-4">Status & Visibility</h3>

                    <div class="space-y-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" id="status"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-admin-teal focus:border-admin-teal">
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive (Draft)</option>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active (Published)</option>
                            </select>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="featured" id="featured" value="1"
                                {{ old('featured') ? 'checked' : '' }}
                                class="rounded border-gray-300 text-admin-teal focus:ring-admin-teal mr-2">
                            <label for="featured" class="text-sm text-gray-700">Featured product</label>
                        </div>
                    </div>
                </div>

                {{-- Categories --}}
                <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
                    <h3 class="text-sm font-bold text-gray-900 mb-4">Categories *</h3>
                    @error('category_ids') <p class="text-red-500 text-xs mb-2">{{ $message }}</p> @enderror
                    @error('primary_category_id') <p class="text-red-500 text-xs mb-2">{{ $message }}</p> @enderror

                    <x-admin.category-tree-checkbox
                        :categories="$allCategories"
                        :selected="old('category_ids', [])"
                        :primary="old('primary_category_id')"
                        name="category_ids"
                        primaryName="primary_category_id"
                    />
                </div>

                {{-- Pricing Summary --}}
                <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
                    <h3 class="text-sm font-bold text-gray-900 mb-4">Pricing</h3>

                    <div>
                        <label for="profit_margin" class="block text-sm font-medium text-gray-700 mb-1">Default Profit Margin %</label>
                        <input type="number" name="profit_margin" id="profit_margin"
                            value="{{ old('profit_margin', 50) }}"
                            min="0" max="100" step="1"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-admin-teal focus:border-admin-teal">
                        <p class="text-xs text-gray-400 mt-1">Stored for reference. Actual prices are set per variant.</p>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
                    <button type="submit" class="btn-admin-primary w-full">
                        <i class="fas fa-plus mr-2"></i>Create Product
                    </button>
                    <p class="text-xs text-gray-400 text-center mt-2">You can upload designs and generate mockups after creation.</p>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function printfulSetup() {
    const variants = @json($variants);
    const defaultMarkupPct = 50;

    // Initialize state
    const selectedVariants = {};
    const retailPrices = {};

    variants.forEach((v, i) => {
        selectedVariants[i] = false;
        const cost = parseFloat(v.price || 0);
        retailPrices[i] = parseFloat((cost * (1 + defaultMarkupPct / 100)).toFixed(2));
    });

    return {
        selectedVariants,
        retailPrices,
        variantData: variants,
        defaultMarkup: defaultMarkupPct,

        selectAllVariants() {
            Object.keys(this.selectedVariants).forEach(k => this.selectedVariants[k] = true);
        },

        deselectAllVariants() {
            Object.keys(this.selectedVariants).forEach(k => this.selectedVariants[k] = false);
        },

        toggleAllVariants(checked) {
            Object.keys(this.selectedVariants).forEach(k => this.selectedVariants[k] = checked);
        },

        applyMarkupToAll() {
            const markup = this.defaultMarkup / 100;
            variants.forEach((v, i) => {
                const cost = parseFloat(v.price || 0);
                this.retailPrices[i] = parseFloat((cost * (1 + markup)).toFixed(2));
            });
        },
    };
}
</script>
@endsection
