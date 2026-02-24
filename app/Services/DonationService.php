<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\DonationTier;
use App\Models\RecurringDonation;
use App\Mail\DonationThankYouMail;
use App\Mail\DonationReceiptMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class DonationService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create a Stripe Checkout session for a one-time donation.
     */
    public function createCheckoutSession(array $data): StripeSession
    {
        $donation = $this->createPendingDonation($data);

        $sessionParams = [
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'client_reference_id' => 'DON-' . $donation->id,
            'customer_email' => $donation->donor_email,
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'unit_amount' => (int) ($donation->amount * 100),
                    'product_data' => [
                        'name' => 'Donation to Chains for Hebb',
                        'description' => $donation->tier ? "Tier: {$donation->tier->name}" : 'Custom donation',
                    ],
                ],
                'quantity' => 1,
            ]],
            'success_url' => route('donate.success', $donation) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('donate.index'),
            'metadata' => [
                'donation_id' => $donation->id,
                'type' => 'donation',
            ],
        ];

        $session = StripeSession::create($sessionParams);

        $donation->update(['stripe_session_id' => $session->id]);

        return $session;
    }

    /**
     * Create a Stripe Checkout session for a recurring donation.
     */
    public function createRecurringCheckoutSession(array $data): StripeSession
    {
        $donation = $this->createPendingDonation(array_merge($data, ['donation_type' => 'recurring']));

        $interval = $data['interval'] ?? 'month';
        $stripeInterval = match($interval) {
            'monthly' => 'month',
            'quarterly' => 'month',
            'yearly' => 'year',
            default => 'month',
        };
        $intervalCount = $interval === 'quarterly' ? 3 : 1;

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'mode' => 'subscription',
            'client_reference_id' => 'DON-' . $donation->id,
            'customer_email' => $donation->donor_email,
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'unit_amount' => (int) ($donation->amount * 100),
                    'recurring' => [
                        'interval' => $stripeInterval,
                        'interval_count' => $intervalCount,
                    ],
                    'product_data' => [
                        'name' => 'Recurring Donation to Chains for Hebb',
                    ],
                ],
                'quantity' => 1,
            ]],
            'success_url' => route('donate.success', $donation) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('donate.index'),
            'metadata' => [
                'donation_id' => $donation->id,
                'type' => 'recurring_donation',
                'interval' => $interval,
            ],
        ]);

        $donation->update(['stripe_session_id' => $session->id]);

        return $session;
    }

    /**
     * Handle a successful one-time donation payment.
     */
    public function handlePaymentSuccess(Donation $donation, string $paymentIntentId): void
    {
        $donation->update([
            'payment_status' => 'paid',
            'stripe_payment_intent_id' => $paymentIntentId,
            'tax_receipt_number' => $this->generateReceiptNumber(),
        ]);

        $this->sendThankYouEmail($donation);

        Log::info("Donation {$donation->id} marked as paid. Amount: \${$donation->amount}");
    }

    /**
     * Handle a successful recurring donation subscription.
     */
    public function handleSubscriptionCreated(Donation $donation, $subscription): void
    {
        $donation->update([
            'payment_status' => 'paid',
            'stripe_subscription_id' => $subscription->id,
            'tax_receipt_number' => $this->generateReceiptNumber(),
        ]);

        RecurringDonation::create([
            'donation_id' => $donation->id,
            'stripe_subscription_id' => $subscription->id,
            'amount' => $donation->amount,
            'interval' => $donation->tier?->name ?? 'monthly',
            'status' => 'active',
            'current_period_start' => now(),
            'current_period_end' => now()->addMonth(),
        ]);

        $this->sendThankYouEmail($donation);

        Log::info("Recurring donation {$donation->id} activated. Amount: \${$donation->amount}/month");
    }

    /**
     * Get donor wall data.
     */
    public function getDonorWall(int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return Donation::paid()
            ->with('tier')
            ->orderByDesc('amount')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get donation statistics.
     */
    public function getStats(): array
    {
        return [
            'total_raised' => Donation::paid()->sum('amount'),
            'total_donors' => Donation::paid()->distinct('donor_email')->count('donor_email'),
            'donation_count' => Donation::paid()->count(),
            'average_donation' => Donation::paid()->avg('amount') ?? 0,
            'recurring_mrr' => RecurringDonation::active()->sum('amount'),
            'recurring_count' => RecurringDonation::active()->count(),
        ];
    }

    protected function createPendingDonation(array $data): Donation
    {
        return Donation::create([
            'donor_name' => $data['donor_name'],
            'donor_email' => $data['donor_email'],
            'customer_id' => $data['customer_id'] ?? null,
            'amount' => $data['amount'],
            'donation_type' => $data['donation_type'] ?? 'one_time',
            'tier_id' => $data['tier_id'] ?? null,
            'is_anonymous' => $data['is_anonymous'] ?? false,
            'donor_message' => $data['donor_message'] ?? null,
            'display_name' => $data['display_name'] ?? null,
            'payment_status' => 'pending',
        ]);
    }

    protected function generateReceiptNumber(): string
    {
        return 'CFH-' . now()->format('Y') . '-' . strtoupper(Str::random(8));
    }

    protected function sendThankYouEmail(Donation $donation): void
    {
        try {
            Mail::to($donation->donor_email)->send(new DonationThankYouMail($donation));
        } catch (\Exception $e) {
            Log::error("Failed to send donation thank you email for donation {$donation->id}: {$e->getMessage()}");
        }
    }
}
