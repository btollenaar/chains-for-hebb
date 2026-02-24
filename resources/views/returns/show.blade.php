@extends('layouts.app')

@section('title', 'Return Request #' . $return->return_number)

@section('content')
<div class="min-h-screen py-8" style="background: var(--surface);">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm mb-6" style="color: var(--on-surface-muted);">
            <a href="{{ route('orders.index') }}" class="hover:underline">My Orders</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <a href="{{ route('orders.show', $return->order) }}" class="hover:underline">Order #{{ $return->order->order_number }}</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span style="color: var(--on-surface);">Return #{{ $return->return_number }}</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold font-display" style="color: var(--on-surface);">Return #{{ $return->return_number }}</h1>
                <p class="mt-1" style="color: var(--on-surface-muted);">Submitted {{ $return->created_at->format('M j, Y \a\t g:i A') }}</p>
            </div>
            @php
                $statusColors = [
                    'requested' => 'bg-yellow-100 text-yellow-800',
                    'approved' => 'bg-green-100 text-green-800',
                    'rejected' => 'bg-red-100 text-red-800',
                    'completed' => 'bg-blue-100 text-blue-800',
                ];
            @endphp
            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold {{ $statusColors[$return->status] ?? 'bg-gray-100 text-gray-800' }}">
                <i class="fas fa-circle text-xs mr-2"></i>
                {{ ucfirst($return->status) }}
            </span>
        </div>

        <div class="space-y-6">
            {{-- Status Card --}}
            @if($return->is_approved)
                <div class="card-glass rounded-2xl p-6" style="border-left: 4px solid #10B981;">
                    <h3 class="font-semibold text-earth-success mb-2"><i class="fas fa-check-circle mr-2"></i>Return Approved</h3>
                    <p style="color: var(--on-surface-muted);">
                        Refund of <strong style="color: var(--on-surface);">${{ number_format($return->refund_amount, 2) }}</strong>
                        via {{ ucfirst(str_replace('_', ' ', $return->refund_method)) }}.
                    </p>
                    @if($return->admin_notes)
                        <p class="mt-2 text-sm" style="color: var(--on-surface-muted);">
                            <strong>Note:</strong> {{ $return->admin_notes }}
                        </p>
                    @endif
                </div>
            @elseif($return->is_rejected)
                <div class="card-glass rounded-2xl p-6" style="border-left: 4px solid #EF4444;">
                    <h3 class="font-semibold text-red-500 mb-2"><i class="fas fa-times-circle mr-2"></i>Return Request Declined</h3>
                    @if($return->admin_notes)
                        <p style="color: var(--on-surface-muted);">{{ $return->admin_notes }}</p>
                    @endif
                </div>
            @elseif($return->is_completed)
                <div class="card-glass rounded-2xl p-6" style="border-left: 4px solid #374151;">
                    <h3 class="font-semibold text-earth-green mb-2"><i class="fas fa-check-double mr-2"></i>Refund Processed</h3>
                    <p style="color: var(--on-surface-muted);">
                        Your refund of <strong style="color: var(--on-surface);">${{ number_format($return->refund_amount, 2) }}</strong> has been processed.
                        @if($return->refund_method === 'original')
                            Please allow 5-10 business days for the refund to appear on your statement.
                        @endif
                    </p>
                </div>
            @else
                <div class="card-glass rounded-2xl p-6" style="border-left: 4px solid #F59E0B;">
                    <h3 class="font-semibold text-earth-amber mb-2"><i class="fas fa-clock mr-2"></i>Under Review</h3>
                    <p style="color: var(--on-surface-muted);">Your return request is being reviewed. We'll notify you by email once a decision has been made.</p>
                </div>
            @endif

            {{-- Return Details --}}
            <div class="card-glass rounded-2xl p-6">
                <h2 class="text-lg font-semibold mb-4" style="color: var(--on-surface);">Return Details</h2>

                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium" style="color: var(--on-surface-muted);">Reason</dt>
                        <dd class="mt-1" style="color: var(--on-surface);">{{ \App\Models\ReturnRequest::reasonOptions()[$return->reason] ?? $return->reason }}</dd>
                    </div>

                    @if($return->details)
                        <div>
                            <dt class="text-sm font-medium" style="color: var(--on-surface-muted);">Details</dt>
                            <dd class="mt-1" style="color: var(--on-surface);">{{ $return->details }}</dd>
                        </div>
                    @endif

                    @if($return->items && count($return->items) > 0)
                        <div>
                            <dt class="text-sm font-medium mb-2" style="color: var(--on-surface-muted);">Items Being Returned</dt>
                            <dd>
                                <ul class="space-y-2">
                                    @foreach($return->items as $item)
                                        <li class="flex items-center gap-2 text-sm" style="color: var(--on-surface);">
                                            <i class="fas fa-box text-xs" style="color: var(--on-surface-muted);"></i>
                                            {{ $item['name'] ?? 'Item' }} x{{ $item['quantity'] ?? 1 }}
                                        </li>
                                    @endforeach
                                </ul>
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Order Summary --}}
            <div class="card-glass rounded-2xl p-6">
                <h2 class="text-lg font-semibold mb-4" style="color: var(--on-surface);">Original Order</h2>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium" style="color: var(--on-surface);">Order #{{ $return->order->order_number }}</p>
                        <p class="text-sm" style="color: var(--on-surface-muted);">{{ $return->order->created_at->format('M j, Y') }} - ${{ number_format($return->order->total_amount, 2) }}</p>
                    </div>
                    <a href="{{ route('orders.show', $return->order) }}" class="btn-glass text-sm" style="color: var(--on-surface);">
                        View Order
                    </a>
                </div>
            </div>

            {{-- Back Link --}}
            <div class="text-center">
                <a href="{{ route('orders.index') }}" class="text-sm hover:underline" style="color: var(--on-surface-muted);">
                    <i class="fas fa-arrow-left mr-1"></i>Back to My Orders
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
