@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Add Sponsor</h1>
        <p class="text-gray-600 mt-1">Create a new sponsor entry</p>
    </div>

    <div class="pb-12">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <form action="{{ route('admin.sponsors.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Name -->
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" required
                               value="{{ old('name') }}"
                               class="w-full border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                        @if($errors->first('name'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('name') }}</p>
                        @endif
                    </div>

                    <!-- Sponsor Tier -->
                    <div class="mb-6">
                        <label for="sponsor_tier_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Sponsor Tier <span class="text-red-500">*</span>
                        </label>
                        <select name="sponsor_tier_id" id="sponsor_tier_id" required
                                class="w-full border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                            <option value="">Select a tier...</option>
                            @foreach($tiers as $tier)
                                <option value="{{ $tier->id }}" {{ old('sponsor_tier_id') == $tier->id ? 'selected' : '' }}>
                                    {{ $tier->name }}
                                </option>
                            @endforeach
                        </select>
                        @if($errors->first('sponsor_tier_id'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('sponsor_tier_id') }}</p>
                        @endif
                    </div>

                    <!-- Logo -->
                    <div class="mb-6">
                        <label for="logo" class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                        <input type="file" name="logo" id="logo" accept="image/*"
                               class="w-full border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                        @if($errors->first('logo'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('logo') }}</p>
                        @endif
                        <p class="mt-1 text-xs text-gray-500">Accepted formats: JPEG, PNG, SVG, WebP. Max 2MB.</p>
                    </div>

                    <!-- Website URL -->
                    <div class="mb-6">
                        <label for="website_url" class="block text-sm font-medium text-gray-700 mb-1">Website URL</label>
                        <input type="url" name="website_url" id="website_url"
                               value="{{ old('website_url') }}"
                               placeholder="https://example.com"
                               class="w-full border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                        @if($errors->first('website_url'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('website_url') }}</p>
                        @endif
                    </div>

                    <!-- Sponsorship Amount -->
                    <div class="mb-6">
                        <label for="sponsorship_amount" class="block text-sm font-medium text-gray-700 mb-1">
                            Sponsorship Amount <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                            <input type="number" name="sponsorship_amount" id="sponsorship_amount" step="0.01" min="0" required
                                   value="{{ old('sponsorship_amount') }}"
                                   class="w-full pl-7 border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                        </div>
                        @if($errors->first('sponsorship_amount'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('sponsorship_amount') }}</p>
                        @endif
                    </div>

                    <!-- Dates Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Sponsorship Date -->
                        <div>
                            <label for="sponsorship_date" class="block text-sm font-medium text-gray-700 mb-1">
                                Sponsorship Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="sponsorship_date" id="sponsorship_date" required
                                   value="{{ old('sponsorship_date') }}"
                                   class="w-full border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                            @if($errors->first('sponsorship_date'))
                                <p class="mt-1 text-sm text-red-600">{{ $errors->first('sponsorship_date') }}</p>
                            @endif
                        </div>

                        <!-- Expires At -->
                        <div>
                            <label for="sponsorship_expires_at" class="block text-sm font-medium text-gray-700 mb-1">Expires At</label>
                            <input type="date" name="sponsorship_expires_at" id="sponsorship_expires_at"
                                   value="{{ old('sponsorship_expires_at') }}"
                                   class="w-full border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                            @if($errors->first('sponsorship_expires_at'))
                                <p class="mt-1 text-sm text-red-600">{{ $errors->first('sponsorship_expires_at') }}</p>
                            @endif
                            <p class="mt-1 text-xs text-gray-500">Leave blank for no expiration.</p>
                        </div>
                    </div>

                    <!-- Checkboxes Row -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <!-- Is Active -->
                        <div class="flex items-center">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" id="is_active" value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-admin-teal focus:ring-admin-teal border-gray-300 rounded">
                            <label for="is_active" class="ml-2 text-sm text-gray-700">Active</label>
                        </div>

                        <!-- Is Featured -->
                        <div class="flex items-center">
                            <input type="hidden" name="is_featured" value="0">
                            <input type="checkbox" name="is_featured" id="is_featured" value="1"
                                   {{ old('is_featured') ? 'checked' : '' }}
                                   class="h-4 w-4 text-admin-teal focus:ring-admin-teal border-gray-300 rounded">
                            <label for="is_featured" class="ml-2 text-sm text-gray-700">Featured</label>
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
                        <a href="{{ route('admin.sponsors.index') }}" class="btn-admin-secondary">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Sponsors
                        </a>
                        <button type="submit" class="btn-admin-primary">
                            <i class="fas fa-save mr-2"></i>Create Sponsor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
