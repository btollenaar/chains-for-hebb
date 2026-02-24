@extends('layouts.app')

@section('title', 'Request Return - Order #' . $order->order_number)

@section('content')
<div class="min-h-screen py-8" style="background: var(--surface);">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm mb-6" style="color: var(--on-surface-muted);">
            <a href="{{ route('orders.index') }}" class="hover:underline">My Orders</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <a href="{{ route('orders.show', $order) }}" class="hover:underline">Order #{{ $order->order_number }}</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span style="color: var(--on-surface);">Request Return</span>
        </nav>

        <h1 class="text-2xl md:text-3xl font-bold font-display mb-2" style="color: var(--on-surface);">Request a Return</h1>
        <p class="mb-8" style="color: var(--on-surface-muted);">Order #{{ $order->order_number }} - Placed {{ $order->created_at->format('M j, Y') }}</p>

        <form action="{{ route('returns.store', $order) }}" method="POST">
            @csrf

            <div class="card-glass rounded-2xl p-6 md:p-8 space-y-6">

                {{-- Order Items Selection --}}
                <div>
                    <label class="block text-sm font-semibold mb-3" style="color: var(--on-surface);">
                        Which items are you returning? <span style="color: var(--on-surface-muted);">(optional - select specific items or leave blank for entire order)</span>
                    </label>
                    <div class="space-y-3">
                        @foreach($order->items as $item)
                            <label class="flex items-center gap-3 p-3 rounded-xl cursor-pointer transition-colors" style="background: var(--surface-raised);">
                                <input type="checkbox" name="items[]" value="{{ $item->id }}"
                                       class="rounded text-earth-primary focus:ring-earth-primary/50">
                                <div class="flex-1">
                                    <span class="font-medium" style="color: var(--on-surface);">{{ $item->name }}</span>
                                    <span class="text-sm ml-2" style="color: var(--on-surface-muted);">x{{ $item->quantity }} - ${{ number_format($item->subtotal, 2) }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Reason --}}
                <div>
                    <label for="reason" class="block text-sm font-semibold mb-2" style="color: var(--on-surface);">
                        Reason for Return <span class="text-red-500">*</span>
                    </label>
                    <select name="reason" id="reason" required
                            class="input-glass rounded-xl w-full">
                        <option value="">Select a reason...</option>
                        @foreach($reasons as $value => $label)
                            <option value="{{ $value }}" {{ old('reason') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('reason')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Details --}}
                <div>
                    <label for="details" class="block text-sm font-semibold mb-2" style="color: var(--on-surface);">
                        Additional Details
                    </label>
                    <textarea name="details" id="details" rows="4" maxlength="2000"
                              class="input-glass rounded-xl w-full"
                              placeholder="Please describe the issue in detail...">{{ old('details') }}</textarea>
                    @error('details')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Info --}}
                <div class="flex items-start gap-3 p-4 rounded-xl" style="background: rgba(45, 106, 79, 0.1); border: 1px solid rgba(45, 106, 79, 0.2);">
                    <i class="fas fa-info-circle text-earth-green mt-0.5"></i>
                    <div class="text-sm" style="color: var(--on-surface);">
                        <p class="font-semibold mb-1">What happens next?</p>
                        <ul class="space-y-1" style="color: var(--on-surface-muted);">
                            <li>Our team will review your return request within 1-2 business days.</li>
                            <li>You'll receive an email when your request is approved or if we need more information.</li>
                            <li>Approved refunds are typically processed within 5-10 business days.</li>
                        </ul>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col sm:flex-row gap-3 pt-4">
                    <button type="submit" class="btn-gradient">
                        <i class="fas fa-undo mr-2"></i>Submit Return Request
                    </button>
                    <a href="{{ route('orders.show', $order) }}" class="btn-glass text-center" style="color: var(--on-surface);">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
