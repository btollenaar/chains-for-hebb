<?php

namespace App\Http\Controllers;

use App\Jobs\FulfillOrder;
use App\Models\Donation;
use App\Models\Order;
use App\Models\RecurringDonation;
use App\Services\DonationService;
use App\Services\FundraisingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    /**
     * Handle incoming Stripe webhooks
     */
    public function handle(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $webhookSecret
            );
        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe webhook invalid payload: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook invalid signature: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        try {
            switch ($event->type) {
                case 'checkout.session.completed':
                    $this->handleCheckoutSessionCompleted($event->data->object);
                    break;

                case 'payment_intent.succeeded':
                    $this->handlePaymentIntentSucceeded($event->data->object);
                    break;

                case 'payment_intent.payment_failed':
                    $this->handlePaymentIntentFailed($event->data->object);
                    break;

                case 'customer.subscription.created':
                    $this->handleSubscriptionCreated($event->data->object);
                    break;

                case 'customer.subscription.deleted':
                    $this->handleSubscriptionDeleted($event->data->object);
                    break;

                case 'invoice.paid':
                    $this->handleInvoicePaid($event->data->object);
                    break;

                case 'invoice.payment_failed':
                    $this->handleInvoicePaymentFailed($event->data->object);
                    break;

                default:
                    Log::info('Unhandled Stripe webhook event type: ' . $event->type);
            }

            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            Log::error('Stripe webhook processing error: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Handle checkout session completed — routes by client_reference_id prefix.
     * DON-{id} = donation, numeric = order
     */
    protected function handleCheckoutSessionCompleted($session)
    {
        $referenceId = $session->client_reference_id;

        if (!$referenceId) {
            Log::error('Stripe webhook: No client_reference_id in session');
            return;
        }

        // Route donations vs orders by prefix
        if (str_starts_with($referenceId, 'DON-')) {
            $this->handleDonationCheckout($session, $referenceId);
        } else {
            $this->handleOrderCheckout($session, $referenceId);
        }
    }

    /**
     * Handle a donation checkout completion.
     */
    protected function handleDonationCheckout($session, string $referenceId): void
    {
        $donationId = (int) str_replace('DON-', '', $referenceId);
        $donation = Donation::find($donationId);

        if (!$donation) {
            Log::error("Stripe webhook: Donation not found: {$donationId}");
            return;
        }

        if ($donation->payment_status === 'paid') {
            Log::info("Donation {$donationId} already marked as paid, skipping.");
            return;
        }

        $donationService = app(DonationService::class);

        if ($session->mode === 'subscription') {
            $donationService->handleSubscriptionCreated($donation, $session->subscription ?? $session);
        } else {
            $donationService->handlePaymentSuccess($donation, $session->payment_intent);
        }

        // Clear fundraising cache
        app(FundraisingService::class)->clearCache();

        Log::info("Donation {$donationId} processed via Stripe webhook. Amount: \${$donation->amount}");
    }

    /**
     * Handle an order checkout completion (existing flow).
     */
    protected function handleOrderCheckout($session, string $orderId): void
    {
        $order = Order::find($orderId);

        if (!$order) {
            Log::error("Stripe webhook: Order not found: {$orderId}");
            return;
        }

        $order->update([
            'payment_status' => 'paid',
            'stripe_payment_intent_id' => $session->payment_intent,
            'stripe_session_id' => $session->id,
        ]);

        Log::info("Order {$orderId} marked as paid via Stripe webhook");

        if ($order->fulfillment_status === 'pending') {
            FulfillOrder::dispatch($order);
            Log::info("FulfillOrder dispatched for order {$orderId} via Stripe webhook");
        }

        // Clear fundraising cache (merch profit changed)
        app(FundraisingService::class)->clearCache();
    }

    /**
     * Handle payment intent succeeded event
     */
    protected function handlePaymentIntentSucceeded($paymentIntent)
    {
        $order = Order::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if (!$order) {
            // Could be a donation — check donations table
            $donation = Donation::where('stripe_payment_intent_id', $paymentIntent->id)->first();
            if ($donation && $donation->payment_status !== 'paid') {
                $donation->update(['payment_status' => 'paid']);
                Log::info("Donation {$donation->id} payment confirmed via payment_intent.succeeded");
            }
            return;
        }

        if ($order->payment_status !== 'paid') {
            $order->update(['payment_status' => 'paid']);
            Log::info("Order {$order->id} payment confirmed via payment_intent.succeeded");
        }
    }

    /**
     * Handle payment intent failed event
     */
    protected function handlePaymentIntentFailed($paymentIntent)
    {
        $order = Order::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if ($order) {
            $order->update(['payment_status' => 'failed']);
            Log::warning("Order {$order->id} payment failed: " . ($paymentIntent->last_payment_error->message ?? 'Unknown error'));
            return;
        }

        $donation = Donation::where('stripe_payment_intent_id', $paymentIntent->id)->first();
        if ($donation) {
            $donation->update(['payment_status' => 'failed']);
            Log::warning("Donation {$donation->id} payment failed");
        }
    }

    /**
     * Handle subscription created (recurring donations)
     */
    protected function handleSubscriptionCreated($subscription)
    {
        $recurring = RecurringDonation::where('stripe_subscription_id', $subscription->id)->first();

        if ($recurring) {
            $recurring->update([
                'status' => 'active',
                'current_period_start' => \Carbon\Carbon::createFromTimestamp($subscription->current_period_start),
                'current_period_end' => \Carbon\Carbon::createFromTimestamp($subscription->current_period_end),
            ]);
            Log::info("Recurring donation subscription activated: {$subscription->id}");
        }
    }

    /**
     * Handle subscription deleted (cancellation)
     */
    protected function handleSubscriptionDeleted($subscription)
    {
        $recurring = RecurringDonation::where('stripe_subscription_id', $subscription->id)->first();

        if ($recurring) {
            $recurring->update(['status' => 'cancelled']);
            Log::info("Recurring donation subscription cancelled: {$subscription->id}");
        }
    }

    /**
     * Handle invoice paid (recurring donation renewal)
     */
    protected function handleInvoicePaid($invoice)
    {
        if (!$invoice->subscription) {
            return;
        }

        $recurring = RecurringDonation::where('stripe_subscription_id', $invoice->subscription)->first();

        if ($recurring) {
            $recurring->update([
                'current_period_start' => \Carbon\Carbon::createFromTimestamp($invoice->period_start),
                'current_period_end' => \Carbon\Carbon::createFromTimestamp($invoice->period_end),
            ]);

            // Create a new donation record for the renewal
            $original = $recurring->donation;
            if ($original) {
                Donation::create([
                    'donor_name' => $original->donor_name,
                    'donor_email' => $original->donor_email,
                    'customer_id' => $original->customer_id,
                    'amount' => $recurring->amount,
                    'donation_type' => 'recurring',
                    'tier_id' => $original->tier_id,
                    'stripe_payment_intent_id' => $invoice->payment_intent,
                    'payment_status' => 'paid',
                    'is_anonymous' => $original->is_anonymous,
                    'display_name' => $original->display_name,
                ]);

                app(FundraisingService::class)->clearCache();
            }

            Log::info("Recurring donation invoice paid: {$invoice->id}");
        }
    }

    /**
     * Handle invoice payment failed
     */
    protected function handleInvoicePaymentFailed($invoice)
    {
        if (!$invoice->subscription) {
            return;
        }

        $recurring = RecurringDonation::where('stripe_subscription_id', $invoice->subscription)->first();

        if ($recurring) {
            Log::warning("Recurring donation invoice payment failed: {$invoice->id}, subscription: {$invoice->subscription}");
        }
    }
}
