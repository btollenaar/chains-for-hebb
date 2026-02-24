@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Donation Tier</h1>
                <p class="text-gray-600 mt-1">{{ $tier->name }}</p>
            </div>
            <a href="{{ route('admin.donation-tiers.index') }}" class="btn-admin-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Tiers
            </a>
        </div>
    </div>

    <div class="pb-12">
        <div class="max-w-3xl mx-auto">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.donation-tiers.update', $tier) }}">
                @csrf
                @method('PUT')

                <!-- Basic Info -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Basic Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $tier->name) }}" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal sm:text-sm">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                            <input type="text" name="slug" id="slug" value="{{ old('slug', $tier->slug) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal sm:text-sm">
                            <p class="text-xs text-gray-500 mt-1">Leave blank to auto-generate from name</p>
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="suggested_amount" class="block text-sm font-medium text-gray-700 mb-1">Suggested Amount ($) *</label>
                        <input type="number" name="suggested_amount" id="suggested_amount" value="{{ old('suggested_amount', $tier->suggested_amount) }}" required
                               step="0.01" min="0.01"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal sm:text-sm">
                        @error('suggested_amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Description & Perks -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Description & Perks</h3>

                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" id="description" rows="3"
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal sm:text-sm">{{ old('description', $tier->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="perks" class="block text-sm font-medium text-gray-700 mb-1">Perks</label>
                        <textarea name="perks" id="perks" rows="3"
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal sm:text-sm">{{ old('perks', $tier->perks) }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Enter each perk on a new line</p>
                        @error('perks')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Badge & Display -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Badge & Display</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="badge_icon" class="block text-sm font-medium text-gray-700 mb-1">Badge Icon</label>
                            <input type="text" name="badge_icon" id="badge_icon" value="{{ old('badge_icon', $tier->badge_icon) }}"
                                   placeholder="e.g. emoji or icon class"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal sm:text-sm">
                            @error('badge_icon')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="badge_color" class="block text-sm font-medium text-gray-700 mb-1">Badge Color</label>
                            <input type="color" name="badge_color" id="badge_color" value="{{ old('badge_color', $tier->badge_color ?? '#2D6069') }}"
                                   class="w-full h-10 rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal cursor-pointer">
                            @error('badge_color')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $tier->sort_order) }}"
                                   min="0"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal sm:text-sm">
                            <p class="text-xs text-gray-500 mt-1">Lower numbers appear first</p>
                            @error('sort_order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-end pb-1">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1"
                                       {{ old('is_active', $tier->is_active) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-admin-teal shadow-sm focus:ring-admin-teal">
                                <span class="ms-2 text-sm text-gray-700">Active (visible on donation page)</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex justify-end gap-3">
                    <a href="{{ route('admin.donation-tiers.index') }}" class="btn-admin-secondary">Cancel</a>
                    <button type="submit" class="btn-admin-primary">
                        <i class="fas fa-save mr-2"></i>Update Tier
                    </button>
                </div>
            </form>

            <!-- Delete Form (outside the update form) -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <form method="POST" action="{{ route('admin.donation-tiers.destroy', $tier) }}"
                      onsubmit="return confirm('Are you sure you want to delete this tier? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-admin-secondary text-red-600 hover:text-red-800">
                        <i class="fas fa-trash mr-2"></i>Delete Tier
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
