@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Edit Tag</h1>
                <p class="text-gray-600 mt-1">Update tag details for "{{ $tag->name }}"</p>
            </div>
            <a href="{{ route('admin.tags.index') }}" class="btn-admin-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Tags
            </a>
        </div>
    </div>

    <div class="pb-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.tags.update', $tag) }}">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Tag Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $tag->name) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal"
                                   placeholder="e.g., VIP, Wholesale, Brand Fan"
                                   required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Color -->
                        <div class="mb-6">
                            <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                                Tag Color <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center gap-3">
                                <input type="color" name="color" id="color" value="{{ old('color', $tag->color) }}"
                                       class="h-10 w-16 rounded border-gray-300 cursor-pointer">
                                <input type="text" id="color-hex" value="{{ old('color', $tag->color) }}"
                                       class="w-28 rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal text-sm"
                                       pattern="^#[0-9A-Fa-f]{6}$"
                                       maxlength="7"
                                       placeholder="#000000">
                                <span class="inline-block w-8 h-8 rounded-full border border-gray-200" id="color-preview" style="background-color: {{ old('color', $tag->color) }};"></span>
                            </div>
                            @error('color')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Description
                            </label>
                            <textarea name="description" id="description" rows="3"
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal"
                                      placeholder="Brief description of what this tag represents...">{{ old('description', $tag->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tag Info -->
                        <div class="mb-6 bg-gray-50 rounded-lg p-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Tag Info</h4>
                            <div class="text-sm text-gray-600 space-y-1">
                                <p><span class="font-medium">Slug:</span> {{ $tag->slug }}</p>
                                <p><span class="font-medium">Customers:</span> {{ $tag->customer_count }}</p>
                                <p><span class="font-medium">Created:</span> {{ $tag->created_at->format('M d, Y g:i A') }}</p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-3 pt-4 border-t">
                            <button type="submit" class="btn-admin-primary">
                                <i class="fas fa-save mr-2"></i>Update Tag
                            </button>
                            <a href="{{ route('admin.tags.index') }}" class="btn-admin-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg border border-red-200">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-red-600 mb-2">Danger Zone</h3>
                    <p class="text-sm text-gray-600 mb-4">Deleting this tag will remove it from all customers. This action cannot be undone.</p>
                    <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete the tag \'{{ $tag->name }}\'? It will be removed from all {{ $tag->customer_count }} customer(s).');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 font-semibold text-sm">
                            <i class="fas fa-trash mr-2"></i>Delete Tag
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const colorInput = document.getElementById('color');
        const hexInput = document.getElementById('color-hex');
        const preview = document.getElementById('color-preview');

        colorInput.addEventListener('input', function() {
            hexInput.value = this.value;
            preview.style.backgroundColor = this.value;
        });

        hexInput.addEventListener('input', function() {
            if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                colorInput.value = this.value;
                preview.style.backgroundColor = this.value;
            }
        });
    });
</script>
@endpush
