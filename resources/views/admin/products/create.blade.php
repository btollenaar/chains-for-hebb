@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Create Product</h1>
        <p class="text-sm text-gray-600 mt-1">Add a new product to your catalog</p>
    </div>

    <div class="pb-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Basic Information Section -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Basic Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Name -->
                                <div class="md:col-span-2">
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Product Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="name" id="name" required
                                           value="{{ old('name') }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Slug -->
                                <div class="md:col-span-2">
                                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                                        Slug (URL-friendly name)
                                    </label>
                                    <input type="text" name="slug" id="slug"
                                           value="{{ old('slug') }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                                    @error('slug')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-sm text-gray-500">Leave blank to auto-generate from product name</p>
                                </div>

                                <!-- SKU -->
                                <div>
                                    <label for="sku" class="block text-sm font-medium text-gray-700 mb-2">
                                        SKU <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="sku" id="sku" required
                                           value="{{ old('sku') }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                                    @error('sku')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Barcode -->
                                <div>
                                    <label for="barcode" class="block text-sm font-medium text-gray-700 mb-2">
                                        Barcode
                                    </label>
                                    <input type="text" name="barcode" id="barcode"
                                           value="{{ old('barcode') }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                                    @error('barcode')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div class="md:col-span-2">
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                        Short Description
                                    </label>
                                    <textarea name="description" id="description" rows="3"
                                              class="wysiwyg-editor w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">{{ old('description') }}</textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-sm text-gray-500">Brief product description (max 1000 characters)</p>
                                </div>

                                <!-- Long Description -->
                                <div class="md:col-span-2">
                                    <label for="long_description" class="block text-sm font-medium text-gray-700 mb-2">
                                        Detailed Description
                                    </label>
                                    <textarea name="long_description" id="long_description" rows="6"
                                              class="wysiwyg-editor w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">{{ old('long_description') }}</textarea>
                                    @error('long_description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-sm text-gray-500">Detailed product information (max 5000 characters)</p>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing Section -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Pricing</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Price -->
                                <div>
                                    <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                                        Price <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                                        <input type="number" name="price" id="price" step="0.01" min="0" required
                                               value="{{ old('price') }}"
                                               class="w-full pl-7 pr-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                                    </div>
                                    @error('price')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Sale Price -->
                                <div>
                                    <label for="sale_price" class="block text-sm font-medium text-gray-700 mb-2">
                                        Sale Price
                                    </label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                                        <input type="number" name="sale_price" id="sale_price" step="0.01" min="0"
                                               value="{{ old('sale_price') }}"
                                               class="w-full pl-7 pr-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                                    </div>
                                    @error('sale_price')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-sm text-gray-500">Must be less than regular price</p>
                                </div>

                                <!-- Cost -->
                                <div>
                                    <label for="cost" class="block text-sm font-medium text-gray-700 mb-2">
                                        Cost
                                    </label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                                        <input type="number" name="cost" id="cost" step="0.01" min="0"
                                               value="{{ old('cost') }}"
                                               class="w-full pl-7 pr-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                                    </div>
                                    @error('cost')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-sm text-gray-500">Your cost (not shown to customers)</p>
                                </div>
                            </div>
                        </div>

                        <!-- Inventory Section -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Inventory</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Stock Quantity -->
                                <div>
                                    <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-2">
                                        Stock Quantity <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="stock_quantity" id="stock_quantity" min="0" required
                                           value="{{ old('stock_quantity', 0) }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                                    @error('stock_quantity')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Low Stock Threshold -->
                                <div>
                                    <label for="low_stock_threshold" class="block text-sm font-medium text-gray-700 mb-2">
                                        Low Stock Threshold
                                    </label>
                                    <input type="number" name="low_stock_threshold" id="low_stock_threshold" min="0"
                                           value="{{ old('low_stock_threshold', 5) }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                                    @error('low_stock_threshold')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-sm text-gray-500">Alert when stock reaches this level</p>
                                </div>
                            </div>
                        </div>

                        <!-- Categorization Section -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Categorization</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Categories -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Categories <span class="text-red-500">*</span>
                                    </label>
                                    <p class="text-xs text-gray-600 mb-3">
                                        Select all categories this product belongs to. Mark one as primary for display.
                                    </p>

                                    <div class="category-tree-wrapper">
                                        <x-admin.category-tree-checkbox
                                            :categories="$allCategories"
                                            :selectedIds="old('category_ids', [])"
                                            :primaryId="old('primary_category_id')"
                                            name="category_ids"
                                            primaryName="primary_category_id" />
                                    </div>

                                    <p class="mt-2 text-sm text-gray-600">
                                        <a href="{{ route('admin.products.categories.index') }}" target="_blank" class="link-admin-primary hover:underline">
                                            <i class="fas fa-folder mr-1"></i>Manage Product Categories
                                        </a>
                                    </p>

                                    @error('category_ids')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    @error('primary_category_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Tags -->
                                <div class="md:col-span-2">
                                    <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">
                                        Tags
                                    </label>
                                    <input type="text" name="tags" id="tags"
                                           value="{{ old('tags') }}"
                                           placeholder="organic, natural, vegan"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                                    @error('tags')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-sm text-gray-500">Comma-separated tags for filtering and search</p>
                                </div>
                            </div>
                        </div>

                        <!-- Product Images Section -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Product Images</h3>
                            <div>
                                <label for="images" class="block text-sm font-medium text-gray-700 mb-2">
                                    Upload Images
                                </label>
                                <input type="file" name="images[]" id="images" accept="image/*" multiple
                                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                                @error('images')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                @error('images.*')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">Upload up to 5 images (JPEG, PNG, GIF, WebP). First image will be the primary product image. Max 5MB per image.</p>

                                <!-- Image Preview -->
                                <div id="image-preview" class="mt-4 grid grid-cols-2 md:grid-cols-5 gap-4"></div>
                            </div>
                        </div>

                        <!-- SEO Section -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">SEO Settings</h3>
                            <div class="grid grid-cols-1 gap-6">
                                <!-- Meta Title -->
                                <div>
                                    <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">
                                        Meta Title
                                    </label>
                                    <input type="text" name="meta_title" id="meta_title" maxlength="60"
                                           value="{{ old('meta_title') }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                                    @error('meta_title')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-sm text-gray-500">Recommended: 50-60 characters. <span id="meta-title-counter">0/60</span></p>
                                </div>

                                <!-- Meta Description -->
                                <div>
                                    <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">
                                        Meta Description
                                    </label>
                                    <textarea name="meta_description" id="meta_description" rows="2" maxlength="160"
                                              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">{{ old('meta_description') }}</textarea>
                                    @error('meta_description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-sm text-gray-500">Recommended: 150-160 characters. <span id="meta-desc-counter">0/160</span></p>
                                </div>
                            </div>
                        </div>

                        <!-- Status Section -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Status & Visibility</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Status -->
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                        Status <span class="text-red-500">*</span>
                                    </label>
                                    <select name="status" id="status" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-sm text-gray-500">Inactive products are hidden from customers</p>
                                </div>

                                <!-- Featured -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Featured Product
                                    </label>
                                    <div class="flex items-center">
                                        <input type="checkbox" name="featured" id="featured" value="1"
                                               {{ old('featured') ? 'checked' : '' }}
                                               class="h-4 w-4 text-abs-primary focus:ring-abs-primary border-gray-300 rounded">
                                        <label for="featured" class="ml-2 text-sm text-gray-700">
                                            Feature this product on the homepage
                                        </label>
                                    </div>
                                    @error('featured')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-between pt-6 border-t">
                            <a href="{{ route('admin.products.index') }}" class="btn-admin-secondary">
                                <i class="fas fa-arrow-left mr-2"></i>Back to Products
                            </a>
                            <button type="submit" class="btn-admin-primary">
                                <i class="fas fa-save mr-2"></i>Create Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Auto-generate slug from product name
        document.getElementById('name').addEventListener('input', function(e) {
            const slug = e.target.value
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
            document.getElementById('slug').value = slug;
        });

        // Image preview
        document.getElementById('images').addEventListener('change', function(e) {
            const preview = document.getElementById('image-preview');
            preview.innerHTML = '';

            const files = Array.from(e.target.files).slice(0, 5); // Limit to 5 images

            files.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const div = document.createElement('div');
                    div.className = 'relative';
                    div.innerHTML = `
                        <img src="${e.target.result}" alt="Preview ${index + 1}" class="w-full h-32 object-cover rounded">
                        <div class="absolute top-1 left-1 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">
                            ${index === 0 ? 'Primary' : `Image ${index + 1}`}
                        </div>
                    `;
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });

        // Character counters for SEO fields
        function updateCounter(inputId, counterId, maxLength) {
            const input = document.getElementById(inputId);
            const counter = document.getElementById(counterId);

            input.addEventListener('input', () => {
                const length = input.value.length;
                counter.textContent = `${length}/${maxLength}`;
                counter.classList.toggle('text-red-600', length > maxLength);
                counter.classList.toggle('text-gray-500', length <= maxLength);
            });

            // Initial count
            const length = input.value.length;
            counter.textContent = `${length}/${maxLength}`;
        }

        updateCounter('meta_title', 'meta-title-counter', 60);
        updateCounter('meta_description', 'meta-desc-counter', 160);
    </script>

    <x-tinymce-init />
    @endpush
@endsection
