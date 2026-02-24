@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Coupon</h1>
                <p class="text-gray-600 mt-1">{{ $coupon->code }}</p>
            </div>
            <a href="{{ route('admin.coupons.index') }}" class="btn-admin-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Coupons
            </a>
        </div>
    </div>

    <div class="pb-12">
        <div class="max-w-3xl mx-auto">
            <form method="POST" action="{{ route('admin.coupons.update', $coupon) }}">
                @csrf
                @method('PUT')

                <!-- Basic Info -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Basic Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Coupon Code *</label>
                            <input type="text" name="code" id="code" value="{{ old('code', $coupon->code) }}" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal sm:text-sm uppercase"
                                   style="text-transform: uppercase;">
                            @error('code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <input type="text" name="description" id="description" value="{{ old('description', $coupon->description) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal sm:text-sm">
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Discount Type *</label>
                            <select name="type" id="type" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal sm:text-sm">
                                <option value="percentage" {{ old('type', $coupon->type) === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                <option value="fixed" {{ old('type', $coupon->type) === 'fixed' ? 'selected' : '' }}>Fixed Amount ($)</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="value" class="block text-sm font-medium text-gray-700 mb-1">Value *</label>
                            <input type="number" name="value" id="value" value="{{ old('value', $coupon->value) }}" required
                                   step="0.01" min="0.01"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal sm:text-sm">
                            @error('value')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Limits -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Limits</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="min_order_amount" class="block text-sm font-medium text-gray-700 mb-1">Minimum Order Amount ($)</label>
                            <input type="number" name="min_order_amount" id="min_order_amount" value="{{ old('min_order_amount', $coupon->min_order_amount) }}"
                                   step="0.01" min="0"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal sm:text-sm">
                            @error('min_order_amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="max_discount_amount" class="block text-sm font-medium text-gray-700 mb-1">Max Discount Amount ($)</label>
                            <input type="number" name="max_discount_amount" id="max_discount_amount" value="{{ old('max_discount_amount', $coupon->max_discount_amount) }}"
                                   step="0.01" min="0"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal sm:text-sm">
                            @error('max_discount_amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="max_uses" class="block text-sm font-medium text-gray-700 mb-1">Max Total Uses</label>
                            <input type="number" name="max_uses" id="max_uses" value="{{ old('max_uses', $coupon->max_uses) }}"
                                   min="1"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal sm:text-sm">
                            @error('max_uses')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="max_uses_per_customer" class="block text-sm font-medium text-gray-700 mb-1">Max Uses Per Customer</label>
                            <input type="number" name="max_uses_per_customer" id="max_uses_per_customer" value="{{ old('max_uses_per_customer', $coupon->max_uses_per_customer) }}"
                                   min="1"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal sm:text-sm">
                            @error('max_uses_per_customer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Schedule -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Schedule</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="starts_at" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                            <input type="datetime-local" name="starts_at" id="starts_at"
                                   value="{{ old('starts_at', $coupon->starts_at?->format('Y-m-d\TH:i')) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal sm:text-sm">
                            @error('starts_at')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                            <input type="datetime-local" name="expires_at" id="expires_at"
                                   value="{{ old('expires_at', $coupon->expires_at?->format('Y-m-d\TH:i')) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal sm:text-sm">
                            @error('expires_at')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1"
                                   {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-admin-teal shadow-sm focus:ring-admin-teal">
                            <span class="ms-2 text-sm text-gray-700">Active</span>
                        </label>
                    </div>
                </div>

                <!-- Usage Info -->
                @if($coupon->used_count > 0)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        This coupon has been used {{ $coupon->used_count }} time(s). Changes will only affect future uses.
                    </p>
                </div>
                @endif

                <!-- Submit -->
                <div class="flex justify-end gap-3">
                    <a href="{{ route('admin.coupons.index') }}" class="btn-admin-secondary">Cancel</a>
                    <button type="submit" class="btn-admin-primary">
                        <i class="fas fa-save mr-2"></i>Update Coupon
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
