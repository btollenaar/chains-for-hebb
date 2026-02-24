<?php

namespace App\Services;

use App\Mail\ClaimAccountMail;
use App\Mail\OrderConfirmationMail;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\NewsletterSubscription;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * CheckoutService
 *
 * Handles checkout workflow logic including stock validation,
 * cart clearing, newsletter opt-in, and email notifications.
 */
class CheckoutService
{
    /**
     * Validate stock availability for all products in cart
     *
     * @param Collection $cartItems
     * @return array|null Returns error message array if validation fails, null if success
     */
    public function validateStockAvailability(Collection $cartItems): ?array
    {
        foreach ($cartItems as $cartItem) {
            if ($cartItem->item instanceof Product) {
                $product = $cartItem->item;

                // Printful variant products — check variant stock status
                if (isset($cartItem->variant) && $cartItem->variant) {
                    if ($cartItem->variant->stock_status !== 'in_stock') {
                        $variantName = $product->name . ' - ' . $cartItem->variant->display_name;
                        return [
                            'error' => "Sorry, '{$variantName}' is out of stock. Please remove it from your cart."
                        ];
                    }
                    continue;
                }

                // Standard products — check stock quantity
                if ($product->stock_quantity <= 0) {
                    return [
                        'error' => "Sorry, '{$product->name}' is out of stock. Please remove it from your cart."
                    ];
                }

                if ($cartItem->quantity > $product->stock_quantity) {
                    return [
                        'error' => "Only {$product->stock_quantity} units of '{$product->name}' available. Please update your cart."
                    ];
                }
            }
        }

        return null; // All stock checks passed
    }

    /**
     * Process newsletter opt-in during checkout
     *
     * @param bool $optIn
     * @param string $email
     * @param string $name
     * @param int $customerId
     * @return void
     */
    public function processNewsletterOptIn(bool $optIn, string $email, string $name, int $customerId): void
    {
        if ($optIn) {
            NewsletterSubscription::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'customer_id' => $customerId,
                    'source' => 'checkout',
                    'is_active' => true,
                    'subscribed_at' => now(),
                ]
            );
        }
    }

    /**
     * Clear customer's cart after successful order
     *
     * @param string|int $identifier User ID or session ID
     * @param bool $isAuthenticated
     * @return void
     */
    public function clearCustomerCart($identifier, bool $isAuthenticated): void
    {
        Cart::clearCart($identifier, $isAuthenticated);
    }

    /**
     * Send order confirmation email to customer
     *
     * @param Order $order
     * @return void
     */
    public function sendOrderConfirmationEmail(Order $order): void
    {
        try {
            Mail::to($order->customer->email)->send(new OrderConfirmationMail($order));
        } catch (\Exception $e) {
            Log::error('Order confirmation email failed to send', [
                'order_id' => $order->id,
                'customer_email' => $order->customer->email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send account claim email to guest customer
     *
     * @param Customer $customer
     * @param Order $order
     * @return void
     */
    public function sendAccountClaimEmail(Customer $customer, Order $order): void
    {
        // Only send to guest customers (no password set)
        if ($customer->password === null) {
            try {
                Mail::to($customer->email)->send(new ClaimAccountMail($customer, $order));
            } catch (\Exception $e) {
                Log::error('Account claim email failed to send', [
                    'customer_id' => $customer->id,
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Find or create customer record for checkout
     *
     * @param array $validated Validated checkout form data
     * @return Customer
     */
    public function findOrCreateCustomer(array $validated): Customer
    {
        if (Auth::check()) {
            return Auth::user();
        } else {
            // Guest checkout - reuse existing customer by email or create a lightweight record
            return Customer::firstOrCreate(
                ['email' => $validated['email']],
                [
                    'name' => $validated['name'],
                    'phone' => $validated['phone'] ?? null,
                    'password' => null, // Guest customer - no password
                ]
            );
        }
    }

    /**
     * Get cart items with eager loading
     *
     * @param string|int $identifier User ID or session ID
     * @param bool $isAuthenticated
     * @return Collection
     */
    public function getCartItems($identifier, bool $isAuthenticated): Collection
    {
        if ($isAuthenticated) {
            return Cart::forCustomer($identifier)->with(['item', 'variant', 'item.mockups'])->get();
        }

        return Cart::forSession($identifier)->with(['item', 'variant', 'item.mockups'])->get();
    }
}
