@extends('layouts.admin')

@section('title', 'Edit Subscriber List')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <a href="{{ route('admin.subscriber-lists.index') }}" class="text-gray-600 hover:text-admin-teal mr-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Edit Subscriber List</h1>
        </div>
        <p class="text-gray-600 ml-9">Update the details of your custom subscriber list</p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('admin.subscriber-lists.update', $subscriberList) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    List Name <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="name"
                       id="name"
                       value="{{ old('name', $subscriberList->name) }}"
                       required
                       autofocus
                       placeholder="e.g., VIP Customers, Monthly Newsletter"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring focus:ring-admin-teal focus:ring-opacity-50 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-500 text-sm mt-1">This will be visible to you when managing campaigns</p>
            </div>

            <!-- Slug -->
            <div class="mb-6">
                <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                    Slug <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="slug"
                       id="slug"
                       value="{{ old('slug', $subscriberList->slug) }}"
                       required
                       placeholder="e.g., vip-customers, monthly-newsletter"
                       pattern="[a-z0-9-]+"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring focus:ring-admin-teal focus:ring-opacity-50 @error('slug') border-red-500 @enderror">
                @error('slug')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-500 text-sm mt-1">Lowercase letters, numbers, and hyphens only. Used for internal identification.</p>
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description
                </label>
                <textarea name="description"
                          id="description"
                          rows="4"
                          placeholder="Brief description of this subscriber list..."
                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring focus:ring-admin-teal focus:ring-opacity-50 @error('description') border-red-500 @enderror">{{ old('description', $subscriberList->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-500 text-sm mt-1">Optional: Helps you remember the purpose of this list</p>
            </div>

            <!-- Warning Box -->
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800 mb-1">Important Note</h3>
                        <p class="text-sm text-yellow-700">
                            Changing the slug may affect existing integrations or API references. Subscribers in this list will remain unaffected.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Auto-slug generation script -->
            <script>
                document.getElementById('name').addEventListener('input', function(e) {
                    const slug = e.target.value
                        .toLowerCase()
                        .replace(/[^a-z0-9\s-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-')
                        .trim();
                    document.getElementById('slug').value = slug;
                });
            </script>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200">
                <button type="submit" class="btn-admin-primary flex-1 sm:flex-initial">
                    <i class="fas fa-save mr-2"></i>
                    Update List
                </button>
                <a href="{{ route('admin.subscriber-lists.index') }}" class="btn-admin-secondary flex-1 sm:flex-initial text-center">
                    <i class="fas fa-times mr-2"></i>
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Danger Zone -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6 border-l-4 border-red-500">
        <h3 class="text-lg font-semibold text-red-700 mb-2">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            Danger Zone
        </h3>
        <p class="text-gray-700 mb-4">
            Once you delete this list, there is no going back. All subscriber associations with this list will be removed (subscribers themselves will remain).
        </p>
        <form action="{{ route('admin.subscriber-lists.destroy', $subscriberList) }}"
              method="POST"
              onsubmit="return confirm('Are you absolutely sure you want to delete this list? This action cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                <i class="fas fa-trash mr-2"></i>
                Delete This List
            </button>
        </form>
    </div>
</div>
@endsection
