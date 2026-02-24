@extends('layouts.app')

@section('title', 'Shipping Policy')

@section('content')
<div class="min-h-screen py-12" style="background: var(--surface);">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card-glass rounded-2xl p-8 md:p-12">
            <h1 class="text-3xl md:text-4xl font-display font-bold mb-2" style="color: var(--on-surface);">Shipping Policy</h1>
            <p class="text-sm mb-8" style="color: var(--on-surface-muted);">Last updated: {{ date('F j, Y') }}</p>

            <div class="prose max-w-none" style="color: var(--on-surface);">
                <h2>Processing Time</h2>
                <p>Orders are typically processed within <strong>1-3 business days</strong> after payment confirmation. You will receive a confirmation email when your order ships with tracking information.</p>

                <h2>Domestic Shipping (United States)</h2>

                <table>
                    <thead>
                        <tr>
                            <th>Shipping Method</th>
                            <th>Estimated Delivery</th>
                            <th>Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Standard Shipping</td>
                            <td>5-8 business days</td>
                            <td>$4.99</td>
                        </tr>
                        <tr>
                            <td>Expedited Shipping</td>
                            <td>3-5 business days</td>
                            <td>$9.99</td>
                        </tr>
                        <tr>
                            <td>Express Shipping</td>
                            <td>1-2 business days</td>
                            <td>$14.99</td>
                        </tr>
                    </tbody>
                </table>

                <h3>Free Shipping</h3>
                <p>Enjoy <strong>free standard shipping</strong> on all orders of $45 or more within the continental United States.</p>

                <h2>International Shipping</h2>
                <p>We currently ship to the United States and Canada.</p>

                <table>
                    <thead>
                        <tr>
                            <th>Destination</th>
                            <th>Estimated Delivery</th>
                            <th>Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Canada</td>
                            <td>7-14 business days</td>
                            <td>$12.99+</td>
                        </tr>
                    </tbody>
                </table>

                <p><strong>Note:</strong> International orders may be subject to customs duties and taxes, which are the responsibility of the recipient. These charges are determined by your country's customs office.</p>

                <h2>Order Tracking</h2>
                <p>Once your order ships, you will receive an email with your tracking number and a link to track your package. You can also track your order from your <a href="{{ route('orders.index') }}" style="color: var(--on-surface); text-decoration: underline;">account dashboard</a>.</p>
                <p>We ship with trusted carriers including USPS, UPS, FedEx, and DHL depending on your location and the items ordered.</p>

                <h2>Lost or Damaged Packages</h2>
                <p>If your package appears lost or arrives damaged:</p>
                <ol>
                    <li>Check your tracking information for the latest status</li>
                    <li>Contact us within 7 days of the expected delivery date</li>
                    <li>We will work with the carrier to locate your package or file a claim</li>
                    <li>If the package cannot be located, we will send a replacement or issue a full refund</li>
                </ol>

                <h2>Shipping Restrictions</h2>
                <ul>
                    <li>We cannot ship to P.O. boxes for expedited or express orders</li>
                    <li>Some products may have shipping restrictions to certain areas</li>
                    <li>APO/FPO addresses are supported for standard shipping</li>
                </ul>

                <h2>Address Accuracy</h2>
                <p>Please ensure your shipping address is correct when placing your order. We are not responsible for packages delivered to incorrect addresses provided by the customer. If you need to update your shipping address, contact us as soon as possible — we can make changes only before the order has shipped.</p>

                <h2>Questions?</h2>
                <p>If you have questions about shipping, please contact us:</p>
                <p>
                    Email: {{ \App\Models\Setting::get('contact.email', config('business.contact.email', 'support@example.com')) }}<br>
                    Phone: {{ \App\Models\Setting::get('contact.phone', config('business.contact.phone', '')) }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
