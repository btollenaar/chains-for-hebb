@extends('layouts.app')

@section('title', 'Privacy Policy')

@section('content')
<div class="min-h-screen py-12" style="background: var(--surface);">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card-glass rounded-2xl p-8 md:p-12">
            <h1 class="text-3xl md:text-4xl font-display font-bold mb-2" style="color: var(--on-surface);">Privacy Policy</h1>
            <p class="text-sm mb-8" style="color: var(--on-surface-muted);">Last updated: {{ date('F j, Y') }}</p>

            <div class="prose max-w-none" style="color: var(--on-surface);">
                <h2>1. Information We Collect</h2>
                <p>When you visit our website or make a purchase, we collect certain information about you, including:</p>
                <ul>
                    <li><strong>Personal information:</strong> Name, email address, phone number, shipping and billing address</li>
                    <li><strong>Payment information:</strong> Credit card details are processed securely by Stripe and are never stored on our servers</li>
                    <li><strong>Account information:</strong> If you create an account, your login credentials and order history</li>
                    <li><strong>Usage data:</strong> How you interact with our website, pages visited, and products viewed</li>
                </ul>

                <h2>2. How We Use Your Information</h2>
                <p>We use the information we collect to:</p>
                <ul>
                    <li>Process and fulfill your orders</li>
                    <li>Send order confirmations and shipping updates</li>
                    <li>Provide customer support</li>
                    <li>Send marketing communications (with your consent)</li>
                    <li>Improve our website and product offerings</li>
                    <li>Prevent fraud and protect our business</li>
                </ul>

                <h2>3. Third-Party Services</h2>
                <p>We share your information with the following third parties only as necessary to provide our services:</p>
                <ul>
                    <li><strong>Stripe:</strong> Payment processing</li>
                    <li><strong>Fulfillment partners:</strong> Shipping and product fulfillment (name and shipping address only)</li>
                    <li><strong>Email service providers:</strong> Order confirmations and marketing emails</li>
                    <li><strong>Google Analytics:</strong> Website usage analytics (anonymized)</li>
                    <li><strong>Meta (Facebook):</strong> Advertising and conversion tracking</li>
                </ul>
                <p>We do not sell your personal information to third parties.</p>

                <h2>4. Cookies and Tracking</h2>
                <p>We use cookies and similar technologies to:</p>
                <ul>
                    <li>Keep you signed in to your account</li>
                    <li>Remember items in your shopping cart</li>
                    <li>Analyze website traffic (Google Analytics)</li>
                    <li>Deliver relevant advertising (Meta Pixel)</li>
                </ul>
                <p>You can control cookies through your browser settings. Disabling cookies may affect some features of our website.</p>

                <h2>5. Data Security</h2>
                <p>We implement appropriate security measures to protect your personal information, including:</p>
                <ul>
                    <li>SSL/TLS encryption for all data transmission</li>
                    <li>Secure payment processing via Stripe (PCI DSS compliant)</li>
                    <li>Regular security updates and monitoring</li>
                    <li>Access controls for employee access to customer data</li>
                </ul>

                <h2>6. Your Rights</h2>
                <p>You have the right to:</p>
                <ul>
                    <li><strong>Access:</strong> Request a copy of the personal information we hold about you</li>
                    <li><strong>Correction:</strong> Request correction of inaccurate information</li>
                    <li><strong>Deletion:</strong> Request deletion of your personal information</li>
                    <li><strong>Opt-out:</strong> Unsubscribe from marketing communications at any time</li>
                </ul>

                <h2>7. California Privacy Rights (CCPA)</h2>
                <p>If you are a California resident, you have additional rights under the California Consumer Privacy Act:</p>
                <ul>
                    <li>Right to know what personal information is collected, used, shared, or sold</li>
                    <li>Right to delete personal information held by businesses</li>
                    <li>Right to opt-out of the sale of personal information</li>
                    <li>Right to non-discrimination for exercising your CCPA rights</li>
                </ul>

                <h2>8. Children's Privacy</h2>
                <p>Our website is not intended for children under 13 years of age. We do not knowingly collect personal information from children under 13.</p>

                <h2>9. Changes to This Policy</h2>
                <p>We may update this Privacy Policy from time to time. We will notify you of any material changes by posting the new policy on this page and updating the "Last updated" date.</p>

                <h2>10. Contact Us</h2>
                <p>If you have questions about this Privacy Policy or your personal information, please contact us at:</p>
                <p>
                    <strong>{{ config('business.profile.name', config('app.name')) }}</strong><br>
                    Email: {{ \App\Models\Setting::get('contact.email', config('business.contact.email', 'support@example.com')) }}<br>
                    Phone: {{ \App\Models\Setting::get('contact.phone', config('business.contact.phone', '')) }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
