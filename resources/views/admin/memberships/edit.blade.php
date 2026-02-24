@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <a href="{{ route('admin.memberships.index') }}" class="text-sm text-admin-teal hover:underline mb-2 inline-block">
            <i class="fas fa-arrow-left mr-1"></i>Back to Memberships
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Edit {{ $tier->name }}</h1>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6 max-w-2xl">
        <form action="{{ route('admin.memberships.update', $tier) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tier Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $tier->name) }}" required
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal text-sm">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="badge_color" class="block text-sm font-medium text-gray-700 mb-1">Badge Color</label>
                    <div class="flex gap-2">
                        <input type="color" name="badge_color" id="badge_color" value="{{ old('badge_color', $tier->badge_color) }}"
                               class="h-10 w-14 rounded border-gray-300 cursor-pointer">
                        <input type="text" value="{{ old('badge_color', $tier->badge_color) }}" readonly
                               class="flex-1 rounded-md border-gray-300 shadow-sm text-sm bg-gray-50"
                               id="badge_color_text">
                    </div>
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" id="description" rows="2"
                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal text-sm">{{ old('description', $tier->description) }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price *</label>
                    <input type="number" name="price" id="price" value="{{ old('price', $tier->price) }}" step="0.01" min="0" required
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal text-sm">
                    @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="billing_interval" class="block text-sm font-medium text-gray-700 mb-1">Billing Interval *</label>
                    <select name="billing_interval" id="billing_interval"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal text-sm">
                        <option value="monthly" {{ old('billing_interval', $tier->billing_interval) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="yearly" {{ old('billing_interval', $tier->billing_interval) == 'yearly' ? 'selected' : '' }}>Yearly</option>
                    </select>
                </div>

                <div>
                    <label for="discount_percentage" class="block text-sm font-medium text-gray-700 mb-1">Discount % *</label>
                    <input type="number" name="discount_percentage" id="discount_percentage"
                           value="{{ old('discount_percentage', $tier->discount_percentage) }}"
                           step="0.01" min="0" max="100" required
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal text-sm">
                    @error('discount_percentage') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="features" class="block text-sm font-medium text-gray-700 mb-1">Features (one per line)</label>
                <textarea name="features" id="features" rows="4"
                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal text-sm">{{ old('features', is_array($tier->features) ? implode("\n", $tier->features) : $tier->features) }}</textarea>
                @error('features') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="flex items-center">
                    <input type="hidden" name="priority_booking" value="0">
                    <input type="checkbox" name="priority_booking" id="priority_booking" value="1"
                           {{ old('priority_booking', $tier->priority_booking) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-admin-teal focus:ring-admin-teal">
                    <label for="priority_booking" class="ml-2 text-sm text-gray-700">Priority Booking</label>
                </div>

                <div class="flex items-center">
                    <input type="hidden" name="free_shipping" value="0">
                    <input type="checkbox" name="free_shipping" id="free_shipping" value="1"
                           {{ old('free_shipping', $tier->free_shipping) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-admin-teal focus:ring-admin-teal">
                    <label for="free_shipping" class="ml-2 text-sm text-gray-700">Free Shipping</label>
                </div>

                <div>
                    <label for="display_order" class="block text-sm font-medium text-gray-700 mb-1">Display Order</label>
                    <input type="number" name="display_order" id="display_order" value="{{ old('display_order', $tier->display_order) }}" min="0"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal text-sm">
                </div>
            </div>

            <div>
                <label for="stripe_price_id" class="block text-sm font-medium text-gray-700 mb-1">Stripe Price ID</label>
                <input type="text" name="stripe_price_id" id="stripe_price_id" value="{{ old('stripe_price_id', $tier->stripe_price_id) }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal text-sm"
                       placeholder="price_xxxxxxxxxxxxx">
                <p class="text-xs text-gray-500 mt-1">From Stripe Dashboard. Leave blank for manual management only.</p>
            </div>

            <div class="flex items-center">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       {{ old('is_active', $tier->is_active) ? 'checked' : '' }}
                       class="rounded border-gray-300 text-admin-teal focus:ring-admin-teal">
                <label for="is_active" class="ml-2 text-sm text-gray-700">Active (visible to customers)</label>
            </div>

            <div class="flex gap-3 pt-4 border-t border-gray-200">
                <button type="submit" class="btn-admin-primary">
                    <i class="fas fa-save mr-2"></i>Update Tier
                </button>
                <a href="{{ route('admin.memberships.index') }}" class="btn-admin-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('badge_color').addEventListener('input', function() {
            document.getElementById('badge_color_text').value = this.value;
        });
    </script>
@endsection
