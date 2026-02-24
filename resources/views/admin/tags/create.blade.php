@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Create Tag</h1>
                <p class="text-gray-600 mt-1">Add a new tag for customer segmentation</p>
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
                    <form method="POST" action="{{ route('admin.tags.store') }}">
                        @csrf

                        <!-- Name -->
                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Tag Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
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
                                <input type="color" name="color" id="color" value="{{ old('color', '#6B5F4A') }}"
                                       class="h-10 w-16 rounded border-gray-300 cursor-pointer">
                                <input type="text" id="color-hex" value="{{ old('color', '#6B5F4A') }}"
                                       class="w-28 rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal text-sm"
                                       pattern="^#[0-9A-Fa-f]{6}$"
                                       maxlength="7"
                                       placeholder="#000000">
                                <span class="inline-block w-8 h-8 rounded-full border border-gray-200" id="color-preview" style="background-color: {{ old('color', '#6B5F4A') }};"></span>
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
                                      placeholder="Brief description of what this tag represents...">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-3 pt-4 border-t">
                            <button type="submit" class="btn-admin-primary">
                                <i class="fas fa-save mr-2"></i>Create Tag
                            </button>
                            <a href="{{ route('admin.tags.index') }}" class="btn-admin-secondary">
                                Cancel
                            </a>
                        </div>
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
