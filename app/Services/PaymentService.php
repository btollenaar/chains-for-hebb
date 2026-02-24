<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

/**
 * PaymentService
 *
 * Handles payment processing for various payment methods.
 * Currently supports: Stripe, PayPal (stub), Cash, Check.
 */
class PaymentService
{
    /**
     * Process Stripe Checkout payment
     *
     * @param Order $order
     * @param Customer $customer
     * @return string Stripe Checkout URL
     * @throws \Exception
     */
    public function processStripePayment(Order $order, Customer $customer): string
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        // Build line items for Stripe
        $lineItems = $this->createStripeLineItems($order);

        $sessionParams = [
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => URL::signedRoute('checkout.success', ['order' => $order->id]) . '&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.cancel') . '?order_id=' . $order->id,
            'customer_email' => $customer->email,
            'client_reference_id' => $order->id,
            'metadata' => [
                'order_id' => $order->id,
                'customer_id' => $customer->id,
            ],
        ];

        // Add discount if coupon was applied
        if ($order->discount_amount > 0) {
            $stripeCoupon = \Stripe\Coupon::create([
                'amount_off' => (int) ($order->discount_amount * 100),
                'currency' => strtolower(config('business.payments.currency')),
                'duration' => 'once',
                'name' => $order->coupon_code ? "Coupon: {$order->coupon_code}" : 'Order Discount',
            ]);
            $sessionParams['discounts'] = [['coupon' => $stripeCoupon->id]];
        }

        $stripeSession = StripeSession::create($sessionParams);

        // Store Stripe session ID in order
        $order->update([
            'stripe_session_id' => $stripeSession->id,
        ]);

        return $stripeSession->url;
    }

    /**
     * Create line items array for Stripe Checkout
     *
     * @param Order $order
     * @return array
     */
    protected function createStripeLineItems(Order $order): array
    {
        $lineItems = [];

        foreach ($order->items as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => strtolower(config('business.payments.currency')),
                    'product_data' => [
                        'name' => $item->name,
                        'description' => $item->description ?? null,
                    ],
                    'unit_amount' => (int)($item->unit_price * 100), // Convert to cents
                ],
                'quantity' => $item->quantity,
            ];
        }

        // Add shipping as a separate line item if applicable
        if ($order->shipping_cost > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => strtolower(config('business.payments.currency')),
                    'product_data' => [
                        'name' => 'Shipping (' . ucfirst($order->shipping_method ?? 'Standard') . ')',
                    ],
                    'unit_amount' => (int)($order->shipping_cost * 100),
                ],
                'quantity' => 1,
            ];
        }

        // Add tax as a separate line item if applicable
        if ($order->tax_amount > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => strtolower(config('business.payments.currency')),
                    'product_data' => [
                        'name' => 'Tax',
                    ],
                    'unit_amount' => (int)($order->tax_amount * 100),
                ],
                'quantity' => 1,
            ];
        }

        return $lineItems;
    }

    /**
     * Verify Stripe payment session and update order
     *
     * @param string $sessionId
     * @param Order $order
     * @return bool True if payment verified
     */
    public function verifyStripePayment(string $sessionId, Order $order): bool
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $session = StripeSession::retrieve($sessionId);

            // Verify the session belongs to this order (strict comparison with type cast)
            if ((int) $session->client_reference_id === $order->id && $session->payment_status === 'paid') {
                // Update order payment status
                $order->update([
                    'payment_status' => 'paid',
                    'stripe_payment_intent_id' => $session->payment_intent,
                ]);

                return true;
            }

            return false;
        } catch (\Exception $e) {
            // Log error but don't fail - webhook will handle this
            Log::error('Stripe session verification failed', [
                'session_id' => $sessionId,
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Process a Stripe refund for an order
     */
    public function processStripeRefund(Order $order, float $amount): \Stripe\Refund
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        return \Stripe\Refund::create([
            'payment_intent' => $order->stripe_payment_intent_id,
            'amount' => (int) ($amount * 100), // Convert to cents
            'metadata' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ],
        ]);
    }

    /**
     * Process PayPal payment (placeholder for future implementation)
     *
     * @param Order $order
     * @param Customer $customer
     * @return string PayPal redirect URL
     * @throws \RuntimeException
     */
    public function processPayPalPayment(Order $order, Customer $customer): string
    {
        throw new \RuntimeException('PayPal payment is not currently available. Please use a different payment method.');
    }
}
