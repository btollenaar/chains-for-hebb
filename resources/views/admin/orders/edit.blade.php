@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Edit Order #{{ $order->order_number }}</h1>
    </div>

    <div class="pb-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <!-- Read-Only Order Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Order Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">Order Number</p>
                            <p class="font-semibold">{{ $order->order_number }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Customer</p>
                            <p class="font-semibold">{{ $order->customer->name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Total Amount</p>
                            <p class="font-semibold text-brand-color">${{ number_format($order->total_amount, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Order Date</p>
                            <p class="font-semibold">{{ $order->created_at->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Items</p>
                            <p class="font-semibold">{{ $order->items->count() }} item(s)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Update Order Status</h3>

                    <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <!-- Payment Status -->
                            <div>
                                <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Payment Status *
                                </label>
                                <select name="payment_status" id="payment_status" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                                    <option value="pending" {{ old('payment_status', $order->payment_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="paid" {{ old('payment_status', $order->payment_status) == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="failed" {{ old('payment_status', $order->payment_status) == 'failed' ? 'selected' : '' }}>Failed</option>
                                    <option value="refunded" {{ old('payment_status', $order->payment_status) == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                </select>
                                @error('payment_status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">Update the payment status of this order</p>
                            </div>

                            <!-- Fulfillment Status -->
                            <div>
                                <label for="fulfillment_status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Fulfillment Status *
                                </label>
                                <select name="fulfillment_status" id="fulfillment_status" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                                    <option value="pending" {{ old('fulfillment_status', $order->fulfillment_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ old('fulfillment_status', $order->fulfillment_status) == 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="shipped" {{ old('fulfillment_status', $order->fulfillment_status) == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="delivered" {{ old('fulfillment_status', $order->fulfillment_status) == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="completed" {{ old('fulfillment_status', $order->fulfillment_status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ old('fulfillment_status', $order->fulfillment_status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('fulfillment_status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">Update the fulfillment status of this order. Changing to "Shipped" or "Delivered" will email the customer.</p>
                            </div>

                            <!-- Tracking Information -->
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <h4 class="text-sm font-bold text-gray-700 mb-4">
                                    <i class="fas fa-truck mr-2"></i>Tracking Information
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="tracking_carrier" class="block text-sm font-medium text-gray-700 mb-1">Carrier</label>
                                        <select name="tracking_carrier" id="tracking_carrier"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                                            <option value="">Select carrier...</option>
                                            @foreach(\App\Models\Order::carrierOptions() as $value => $label)
                                                <option value="{{ $value }}" {{ old('tracking_carrier', $order->tracking_carrier) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('tracking_carrier')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="tracking_number" class="block text-sm font-medium text-gray-700 mb-1">Tracking Number</label>
                                        <input type="text" name="tracking_number" id="tracking_number"
                                               value="{{ old('tracking_number', $order->tracking_number) }}"
                                               placeholder="e.g. 1Z999AA10123456784"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                                        @error('tracking_number')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                @if($order->shipped_at)
                                    <p class="mt-2 text-xs text-gray-500">
                                        <i class="fas fa-info-circle mr-1"></i>Shipped: {{ $order->shipped_at->format('M j, Y g:i A') }}
                                        @if($order->delivered_at)
                                            | Delivered: {{ $order->delivered_at->format('M j, Y g:i A') }}
                                        @endif
                                    </p>
                                @endif
                            </div>

                            <!-- Admin Notes -->
                            <div>
                                <label for="admin_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                    Admin Notes
                                </label>
                                <textarea name="admin_notes" id="admin_notes" rows="6"
                                          placeholder="Internal notes (not visible to customer)"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">{{ old('admin_notes', $order->admin_notes) }}</textarea>
                                @error('admin_notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">Add internal notes about this order (max 5000 characters)</p>
                            </div>

                            <!-- Customer Notes (Read-Only) -->
                            @if($order->notes)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Customer Notes
                                </label>
                                <div class="px-4 py-3 bg-gray-50 border border-gray-300 rounded-md text-sm text-gray-700">
                                    {{ $order->notes }}
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-between mt-8 pt-6 border-t">
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn-admin-secondary">
                                <i class="fas fa-arrow-left mr-2"></i>Back to Order
                            </a>
                            <button type="submit" class="btn-admin-primary">
                                <i class="fas fa-save mr-2"></i>Update Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
