@extends('layouts.app')

@section('title', 'Return & Refund Policy')

@section('content')
<div class="min-h-screen py-12" style="background: var(--surface);">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card-glass rounded-2xl p-8 md:p-12">
            <h1 class="text-3xl md:text-4xl font-display font-bold mb-2" style="color: var(--on-surface);">Return & Refund Policy</h1>
            <p class="text-sm mb-8" style="color: var(--on-surface-muted);">Last updated: {{ date('F j, Y') }}</p>

            <div class="prose max-w-none" style="color: var(--on-surface);">
                <h2>30-Day Return Policy</h2>
                <p>We want you to be completely satisfied with your purchase. If you're not happy with your order, we accept returns within 30 days of delivery.</p>

                <h2>Return Conditions</h2>

                <h3>Unopened Items</h3>
                <ul>
                    <li>Full refund to original payment method</li>
                    <li>Item must be in original, unopened packaging</li>
                    <li>Customer is responsible for return shipping costs</li>
                </ul>

                <h3>Defective or Damaged Items</h3>
                <ul>
                    <li>Full refund OR free replacement at your choice</li>
                    <li>No need to return the defective item</li>
                    <li>Please include a photo of the damage with your request</li>
                </ul>

                <h3>Items Under $25 With Issues</h3>
                <ul>
                    <li>Refund or replacement without needing to return the item</li>
                    <li>We believe in making things right without unnecessary hassle</li>
                </ul>

                <h2>Non-Returnable Items</h2>
                <ul>
                    <li>Gift cards</li>
                    <li>Items marked as "Final Sale"</li>
                    <li>Personalized or custom-made products</li>
                    <li>Items that have been used, washed, or altered</li>
                </ul>

                <h2>How to Request a Return</h2>
                <ol>
                    <li>Email us at <a href="mailto:{{ \App\Models\Setting::get('contact.email', config('business.contact.email', 'support@example.com')) }}" style="color: var(--on-surface); text-decoration: underline;">{{ \App\Models\Setting::get('contact.email', config('business.contact.email', 'support@example.com')) }}</a> with your order number and reason for return</li>
                    <li>Our team will review your request within 1-2 business days</li>
                    <li>Once approved, we'll provide return instructions (if applicable)</li>
                    <li>Ship the item back using the provided instructions</li>
                </ol>

                @auth
                <p>You can also submit a return request directly from your <a href="{{ route('orders.index') }}" style="color: var(--on-surface); text-decoration: underline;">order history</a>.</p>
                @endauth

                <h2>Refund Timeline</h2>
                <ul>
                    <li><strong>Approval:</strong> 1-2 business days after receiving your request</li>
                    <li><strong>Processing:</strong> 3-5 business days after approval</li>
                    <li><strong>Bank statement:</strong> 5-10 business days for refund to appear on your statement</li>
                </ul>
                <p>Refunds are issued to the original payment method used for the purchase.</p>

                <h2>Exchanges</h2>
                <p>We currently do not offer direct exchanges. If you'd like a different item, please return the original item for a refund and place a new order.</p>

                <h2>Questions?</h2>
                <p>If you have any questions about our return policy, please contact us:</p>
                <p>
                    Email: {{ \App\Models\Setting::get('contact.email', config('business.contact.email', 'support@example.com')) }}<br>
                    Phone: {{ \App\Models\Setting::get('contact.phone', config('business.contact.phone', '')) }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
