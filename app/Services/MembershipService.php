<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Membership;
use App\Models\MembershipTier;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class MembershipService
{
    /**
     * Create a Stripe Checkout session for a membership subscription
     */
    public function createCheckoutSession(Customer $customer, MembershipTier $tier): string
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $sessionParams = [
            'payment_method_types' => ['card'],
            'mode' => 'subscription',
            'line_items' => [[
                'price' => $tier->stripe_price_id,
                'quantity' => 1,
            ]],
            'success_url' => route('memberships.success', ['tier' => $tier->id]) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('memberships.index'),
            'customer_email' => $customer->email,
            'metadata' => [
                'customer_id' => $customer->id,
                'tier_id' => $tier->id,
            ],
        ];

        $session = StripeSession::create($sessionParams);

        return $session->url;
    }

    /**
     * Activate a membership after successful Stripe subscription
     */
    public function activateMembership(Customer $customer, MembershipTier $tier, string $stripeSubscriptionId): Membership
    {
        // Cancel any existing active membership
        $this->cancelExistingMembership($customer);

        $expiresAt = $tier->billing_interval === 'yearly'
            ? now()->addYear()
            : now()->addMonth();

        return Membership::create([
            'customer_id' => $customer->id,
            'membership_tier_id' => $tier->id,
            'status' => 'active',
            'stripe_subscription_id' => $stripeSubscriptionId,
            'starts_at' => now(),
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     * Cancel existing active membership for a customer
     */
    public function cancelExistingMembership(Customer $customer): void
    {
        $existing = Membership::where('customer_id', $customer->id)
            ->where('status', 'active')
            ->first();

        if ($existing) {
            $existing->cancel();
        }
    }

    /**
     * Cancel a Stripe subscription
     */
    public function cancelStripeSubscription(Membership $membership): bool
    {
        if (!$membership->stripe_subscription_id) {
            $membership->cancel();
            return true;
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $subscription = \Stripe\Subscription::retrieve($membership->stripe_subscription_id);
            $subscription->cancel();

            $membership->cancel();
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to cancel Stripe subscription', [
                'membership_id' => $membership->id,
                'stripe_subscription_id' => $membership->stripe_subscription_id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get the active membership for a customer
     */
    public function getActiveMembership(Customer $customer): ?Membership
    {
        return Membership::where('customer_id', $customer->id)
            ->where('status', 'active')
            ->with('tier')
            ->first();
    }

    /**
     * Calculate member discount for an order subtotal
     */
    public function calculateDiscount(Customer $customer, float $subtotal): float
    {
        $membership = $this->getActiveMembership($customer);

        if (!$membership || !$membership->tier) {
            return 0;
        }

        return round($subtotal * ($membership->tier->discount_percentage / 100), 2);
    }

    /**
     * Check if customer has an active membership
     */
    public function hasActiveMembership(Customer $customer): bool
    {
        return Membership::where('customer_id', $customer->id)
            ->where('status', 'active')
            ->exists();
    }

    /**
     * Handle Stripe subscription created webhook
     */
    public function handleSubscriptionCreated(object $subscription): void
    {
        $customerId = $subscription->metadata->customer_id ?? null;
        $tierId = $subscription->metadata->tier_id ?? null;

        if (!$customerId || !$tierId) {
            Log::warning('Subscription created webhook missing metadata', [
                'subscription_id' => $subscription->id,
            ]);
            return;
        }

        $customer = Customer::find($customerId);
        $tier = MembershipTier::find($tierId);

        if (!$customer || !$tier) {
            Log::error('Subscription created: customer or tier not found', [
                'customer_id' => $customerId,
                'tier_id' => $tierId,
            ]);
            return;
        }

        // Check if membership already exists for this subscription
        $existing = Membership::where('stripe_subscription_id', $subscription->id)->first();
        if ($existing) {
            return;
        }

        $this->activateMembership($customer, $tier, $subscription->id);
        Log::info("Membership activated for customer {$customerId} via webhook");
    }

    /**
     * Handle Stripe subscription updated webhook
     */
    public function handleSubscriptionUpdated(object $subscription): void
    {
        $membership = Membership::where('stripe_subscription_id', $subscription->id)->first();

        if (!$membership) {
            return;
        }

        $status = match ($subscription->status) {
            'active', 'trialing' => 'active',
            'past_due' => 'past_due',
            'canceled', 'unpaid' => 'cancelled',
            default => $membership->status,
        };

        $membership->update([
            'status' => $status,
            'expires_at' => isset($subscription->current_period_end)
                ? \Carbon\Carbon::createFromTimestamp($subscription->current_period_end)
                : $membership->expires_at,
        ]);

        Log::info("Membership {$membership->id} updated to status: {$status}");
    }

    /**
     * Handle Stripe subscription deleted webhook
     */
    public function handleSubscriptionDeleted(object $subscription): void
    {
        $membership = Membership::where('stripe_subscription_id', $subscription->id)->first();

        if (!$membership) {
            return;
        }

        $membership->cancel();
        Log::info("Membership {$membership->id} cancelled via webhook");
    }

    /**
     * Handle invoice payment failed webhook
     */
    public function handleInvoicePaymentFailed(object $invoice): void
    {
        $subscriptionId = $invoice->subscription ?? null;
        if (!$subscriptionId) {
            return;
        }

        $membership = Membership::where('stripe_subscription_id', $subscriptionId)->first();

        if ($membership) {
            $membership->update(['status' => 'past_due']);
            Log::warning("Membership {$membership->id} marked as past_due due to failed invoice");
        }
    }
}
