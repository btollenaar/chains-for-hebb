@extends('layouts.app')

@section('title', 'Terms of Service')

@section('content')
<div class="min-h-screen py-12" style="background: var(--surface);">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card-glass rounded-2xl p-8 md:p-12">
            <h1 class="text-3xl md:text-4xl font-display font-bold mb-2" style="color: var(--on-surface);">Terms of Service</h1>
            <p class="text-sm mb-8" style="color: var(--on-surface-muted);">Last updated: {{ date('F j, Y') }}</p>

            <div class="prose max-w-none" style="color: var(--on-surface);">
                <h2>1. Acceptance of Terms</h2>
                <p>By accessing and using this website, you accept and agree to be bound by these Terms of Service. If you do not agree with these terms, please do not use our website.</p>

                <h2>2. Account Registration</h2>
                <p>When creating an account, you agree to:</p>
                <ul>
                    <li>Provide accurate and complete information</li>
                    <li>Maintain the security of your account credentials</li>
                    <li>Notify us immediately of any unauthorized use of your account</li>
                    <li>Be responsible for all activities under your account</li>
                </ul>

                <h2>3. Products and Pricing</h2>
                <ul>
                    <li>All prices are listed in US Dollars (USD) unless otherwise specified</li>
                    <li>We reserve the right to change prices at any time without notice</li>
                    <li>Product images are for illustration purposes; actual products may vary slightly</li>
                    <li>We make every effort to display colors accurately, but cannot guarantee your monitor will display colors exactly</li>
                    <li>We reserve the right to limit quantities or refuse orders at our discretion</li>
                </ul>

                <h2>4. Orders and Payment</h2>
                <ul>
                    <li>Placing an order constitutes an offer to purchase, which we may accept or decline</li>
                    <li>Payment is processed securely through Stripe</li>
                    <li>You agree to pay all charges at the prices listed at the time of your order, including applicable taxes and shipping</li>
                    <li>If payment fails, your order will not be processed</li>
                </ul>

                <h2>5. Shipping and Delivery</h2>
                <p>Please refer to our <a href="{{ route('legal.shipping-policy') }}" style="color: var(--on-surface); text-decoration: underline;">Shipping Policy</a> for detailed information about shipping methods, timeframes, and costs.</p>

                <h2>6. Returns and Refunds</h2>
                <p>Please refer to our <a href="{{ route('legal.return-policy') }}" style="color: var(--on-surface); text-decoration: underline;">Return & Refund Policy</a> for detailed information about returns, exchanges, and refunds.</p>

                <h2>7. Intellectual Property</h2>
                <p>All content on this website, including text, images, logos, and designs, is the property of {{ config('business.profile.name', config('app.name')) }} or its licensors and is protected by copyright and intellectual property laws. You may not reproduce, distribute, or use any content without our written permission.</p>

                <h2>8. User Conduct</h2>
                <p>You agree not to:</p>
                <ul>
                    <li>Use our website for any unlawful purpose</li>
                    <li>Submit false or misleading information</li>
                    <li>Interfere with the proper functioning of our website</li>
                    <li>Attempt to gain unauthorized access to any part of our systems</li>
                    <li>Submit reviews or content that is fraudulent, defamatory, or inappropriate</li>
                </ul>

                <h2>9. Limitation of Liability</h2>
                <p>To the maximum extent permitted by law, {{ config('business.profile.name', config('app.name')) }} shall not be liable for any indirect, incidental, special, consequential, or punitive damages arising from your use of our website or products. Our total liability shall not exceed the amount you paid for the product or service giving rise to the claim.</p>

                <h2>10. Disclaimer of Warranties</h2>
                <p>Our products are provided "as is" without warranties of any kind, express or implied, except as required by law. We do not warrant that our website will be uninterrupted, error-free, or free of viruses or other harmful components.</p>

                <h2>11. Indemnification</h2>
                <p>You agree to indemnify and hold harmless {{ config('business.profile.name', config('app.name')) }} from any claims, damages, or expenses arising from your violation of these Terms of Service or your use of our website.</p>

                <h2>12. Governing Law</h2>
                <p>These Terms of Service shall be governed by and construed in accordance with the laws of the state in which {{ config('business.profile.name', config('app.name')) }} is registered, without regard to conflict of law provisions.</p>

                <h2>13. Changes to Terms</h2>
                <p>We may update these Terms of Service from time to time. Continued use of our website after changes constitutes acceptance of the updated terms.</p>

                <h2>14. Contact Us</h2>
                <p>If you have questions about these Terms of Service, please contact us at:</p>
                <p>
                    <strong>{{ config('business.profile.name', config('app.name')) }}</strong><br>
                    Email: {{ \App\Models\Setting::get('contact.email', config('business.contact.email', 'support@example.com')) }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
