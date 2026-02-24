@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Add Sponsor Tier</h1>
        <p class="text-gray-600 mt-1">Create a new sponsorship tier level</p>
    </div>

    <div class="pb-12">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <form action="{{ route('admin.sponsor-tiers.store') }}" method="POST">
                    @csrf

                    <!-- Name -->
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" required
                               value="{{ old('name') }}"
                               placeholder="e.g. Gold, Silver, Bronze"
                               class="w-full border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                        @if($errors->first('name'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('name') }}</p>
                        @endif
                    </div>

                    <!-- Slug -->
                    <div class="mb-6">
                        <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">
                            Slug <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="slug" id="slug" required
                               value="{{ old('slug') }}"
                               placeholder="e.g. gold, silver, bronze"
                               class="w-full border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                        @if($errors->first('slug'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('slug') }}</p>
                        @endif
                        <p class="mt-1 text-xs text-gray-500">URL-friendly identifier. Lowercase, no spaces.</p>
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" id="description" rows="3"
                                  class="w-full border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">{{ old('description') }}</textarea>
                        @if($errors->first('description'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('description') }}</p>
                        @endif
                    </div>

                    <!-- Min Amount -->
                    <div class="mb-6">
                        <label for="min_amount" class="block text-sm font-medium text-gray-700 mb-1">
                            Minimum Amount <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                            <input type="number" name="min_amount" id="min_amount" step="0.01" min="0" required
                                   value="{{ old('min_amount') }}"
                                   class="w-full pl-7 border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                        </div>
                        @if($errors->first('min_amount'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('min_amount') }}</p>
                        @endif
                    </div>

                    <!-- Perks -->
                    <div class="mb-6">
                        <label for="perks" class="block text-sm font-medium text-gray-700 mb-1">Perks</label>
                        <textarea name="perks" id="perks" rows="4"
                                  placeholder="One perk per line..."
                                  class="w-full border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">{{ old('perks') }}</textarea>
                        @if($errors->first('perks'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('perks') }}</p>
                        @endif
                        <p class="mt-1 text-xs text-gray-500">List the benefits included in this tier.</p>
                    </div>

                    <!-- Logo Size & Sort Order Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Logo Size -->
                        <div>
                            <label for="logo_size" class="block text-sm font-medium text-gray-700 mb-1">
                                Logo Size <span class="text-red-500">*</span>
                            </label>
                            <select name="logo_size" id="logo_size" required
                                    class="w-full border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                                <option value="xl" {{ old('logo_size') === 'xl' ? 'selected' : '' }}>Extra Large (XL)</option>
                                <option value="lg" {{ old('logo_size') === 'lg' ? 'selected' : '' }}>Large (LG)</option>
                                <option value="md" {{ old('logo_size', 'md') === 'md' ? 'selected' : '' }}>Medium (MD)</option>
                                <option value="sm" {{ old('logo_size') === 'sm' ? 'selected' : '' }}>Small (SM)</option>
                            </select>
                            @if($errors->first('logo_size'))
                                <p class="mt-1 text-sm text-red-600">{{ $errors->first('logo_size') }}</p>
                            @endif
                        </div>

                        <!-- Sort Order -->
                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                            <input type="number" name="sort_order" id="sort_order" min="0"
                                   value="{{ old('sort_order', 0) }}"
                                   class="w-full border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                            @if($errors->first('sort_order'))
                                <p class="mt-1 text-sm text-red-600">{{ $errors->first('sort_order') }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.sponsor-tiers.index') }}" class="btn-admin-secondary">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Tiers
                        </a>
                        <button type="submit" class="btn-admin-primary">
                            <i class="fas fa-save mr-2"></i>Create Tier
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
