@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Add Milestone</h1>
        <p class="text-gray-600 mt-1">Create a new fundraising milestone</p>
    </div>

    <div class="pb-12">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <form action="{{ route('admin.fundraising.milestones.store') }}" method="POST">
                    @csrf

                    <!-- Title -->
                    <div class="mb-6">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                            Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" id="title" required
                               value="{{ old('title') }}"
                               placeholder="e.g. First $1,000 Raised"
                               class="w-full border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                        @if($errors->first('title'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('title') }}</p>
                        @endif
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

                    <!-- Target Amount -->
                    <div class="mb-6">
                        <label for="target_amount" class="block text-sm font-medium text-gray-700 mb-1">
                            Target Amount <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                            <input type="number" name="target_amount" id="target_amount" step="0.01" min="0" required
                                   value="{{ old('target_amount') }}"
                                   class="w-full pl-7 border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                        </div>
                        @if($errors->first('target_amount'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('target_amount') }}</p>
                        @endif
                    </div>

                    <!-- Icon -->
                    <div class="mb-6">
                        <label for="icon" class="block text-sm font-medium text-gray-700 mb-1">Icon</label>
                        <input type="text" name="icon" id="icon"
                               value="{{ old('icon') }}"
                               placeholder="e.g. fas fa-star, fas fa-trophy"
                               class="w-full border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                        @if($errors->first('icon'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('icon') }}</p>
                        @endif
                        <p class="mt-1 text-xs text-gray-500">Font Awesome icon class (e.g. <code>fas fa-flag</code>).</p>
                    </div>

                    <!-- Is Reached & Sort Order -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="flex items-center">
                            <input type="hidden" name="is_reached" value="0">
                            <input type="checkbox" name="is_reached" id="is_reached" value="1"
                                   {{ old('is_reached') ? 'checked' : '' }}
                                   class="h-4 w-4 text-admin-teal focus:ring-admin-teal border-gray-300 rounded">
                            <label for="is_reached" class="ml-2 text-sm text-gray-700">Milestone Reached</label>
                        </div>
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
                        <a href="{{ route('admin.fundraising.index') }}" class="btn-admin-secondary">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                        </a>
                        <button type="submit" class="btn-admin-primary">
                            <i class="fas fa-save mr-2"></i>Create Milestone
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
