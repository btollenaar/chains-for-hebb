<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Services\MembershipService;

/**
 * OrderFactory Service
 *
 * Handles order creation from cart items.
 * Responsible for building orders, order items, and calculating totals.
 */
class OrderFactory
{
    /**
     * Create an order from cart items
     *
     * @param Customer $customer
     * @param Collection $cartItems
     * @param array $shippingAddress
     * @param array $billingAddress
     * @param string $paymentMethod
     * @param string|null $notes
     * @return Order
     */
    public function createOrderFromCart(
        Customer $customer,
        Collection $cartItems,
        array $shippingAddress,
        array $billingAddress,
        string $paymentMethod,
        ?string $notes = null,
        ?Coupon $coupon = null,
        float $shippingCost = 0,
        ?string $shippingMethod = null,
        float $estimatedWeightOz = 0
    ): Order {
        // Calculate order totals with shipping address for accurate tax
        $totals = $this->calculateOrderTotals($cartItems, $shippingCost, $shippingAddress);

        // Create order
        $order = Order::create([
            'customer_id' => $customer->id,
            'subtotal' => $totals['subtotal'],
            'tax_amount' => $totals['tax'],
            'discount_amount' => 0,
            'shipping_cost' => $shippingCost,
            'shipping_method' => $shippingMethod,
            'estimated_weight_oz' => $estimatedWeightOz,
            'total_amount' => $totals['total'],
            'payment_method' => $paymentMethod,
            'payment_status' => 'pending',
            'fulfillment_status' => 'pending',
            'billing_address' => $billingAddress,
            'shipping_address' => $shippingAddress,
            'notes' => $notes,
        ]);

        // Create order items
        $this->createOrderItems($order, $cartItems);

        // Recalculate order totals to ensure accuracy
        $order->calculateTotals();

        // Apply coupon if provided
        if ($coupon) {
            $couponService = new CouponService();
            $couponService->applyCouponToOrder($order, $coupon, $customer->id);
        }

        // Apply membership discount if customer has active membership
        $membershipService = new MembershipService();
        $memberDiscount = $membershipService->calculateDiscount($customer, $order->subtotal);
        if ($memberDiscount > 0) {
            $order->update([
                'discount_amount' => $order->discount_amount + $memberDiscount,
                'total_amount' => $order->total_amount - $memberDiscount,
            ]);
        }

        return $order;
    }

    /**
     * Create order items from cart items
     *
     * @param Order $order
     * @param Collection $cartItems
     * @return void
     */
    protected function createOrderItems(Order $order, Collection $cartItems): void
    {
        foreach ($cartItems as $cartItem) {
            $orderItem = new OrderItem([
                'order_id' => $order->id,
                'item_type' => $cartItem->item_type,
                'item_id' => $cartItem->item_id,
                'product_variant_id' => $cartItem->product_variant_id ?? null,
                'quantity' => $cartItem->quantity,
                'attributes' => $cartItem->attributes ?? null,
            ]);

            // Snapshot item details — use variant pricing for POD products
            if (isset($cartItem->variant) && $cartItem->variant) {
                $orderItem->name = $cartItem->item->name . ' - ' . $cartItem->variant->display_name;
                $orderItem->description = $cartItem->item->description ?? '';
                $orderItem->unit_price = (float) $cartItem->variant->retail_price;
                $orderItem->subtotal = $orderItem->unit_price * $cartItem->quantity;
                $orderItem->printful_variant_id = $cartItem->variant->printful_variant_id;
                $orderItem->variant_snapshot = [
                    'color' => $cartItem->variant->color_name,
                    'size' => $cartItem->variant->size,
                    'sku' => $cartItem->variant->sku,
                    'printful_variant_id' => $cartItem->variant->printful_variant_id,
                ];

                $taxRate = config('business.payments.tax_rate', 0.0);
                $orderItem->tax_amount = round($orderItem->subtotal * $taxRate, 2);
                $orderItem->total = $orderItem->subtotal + $orderItem->tax_amount;
            } else {
                $orderItem->snapshotItemDetails();
            }
            $orderItem->save();

            // Decrement stock for standard products (POD products handled by Printful)
            if (!(isset($cartItem->variant) && $cartItem->variant) && $cartItem->item instanceof Product) {
                $cartItem->item->decrementStock($cartItem->quantity);
            }
        }
    }

    /**
     * Calculate order totals (subtotal, tax, total)
     *
     * @param Collection $cartItems
     * @param float $shippingCost
     * @param array|null $shippingAddress Shipping address for TaxJar calculation
     * @return array
     */
    public function calculateOrderTotals(Collection $cartItems, float $shippingCost = 0, ?array $shippingAddress = null): array
    {
        $subtotal = 0;

        foreach ($cartItems as $cartItem) {
            if (isset($cartItem->variant) && $cartItem->variant) {
                $itemPrice = (float) $cartItem->variant->retail_price;
            } else {
                $itemPrice = $cartItem->item->sale_price ?? $cartItem->item->price ?? $cartItem->item->base_price;
            }
            $subtotal += $itemPrice * $cartItem->quantity;
        }

        $taxJar = app(TaxJarService::class);
        $taxResult = $taxJar->calculateTax([
            'to_zip' => $shippingAddress['zip'] ?? config('business.contact.address.zip', '97015'),
            'to_state' => $shippingAddress['state'] ?? config('business.contact.address.state', 'OR'),
            'to_city' => $shippingAddress['city'] ?? '',
            'to_country' => $shippingAddress['country'] ?? 'US',
            'amount' => $subtotal,
            'shipping' => $shippingCost,
        ]);

        $tax = $taxResult['amount_to_collect'];
        $total = $subtotal + $tax + $shippingCost;

        return [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'tax_rate' => $taxResult['rate'],
            'shipping' => $shippingCost,
            'total' => $total,
        ];
    }

    /**
     * Build address arrays from validated request data
     *
     * @param array $validated
     * @param bool $sameAsShipping
     * @return array ['shipping' => [...], 'billing' => [...]]
     */
    public function buildAddressArrays(array $validated, bool $sameAsShipping): array
    {
        $shippingAddress = [
            'street' => $validated['shipping_street'],
            'city' => $validated['shipping_city'],
            'state' => $validated['shipping_state'],
            'zip' => $validated['shipping_zip'],
            'country' => $validated['shipping_country'],
        ];

        $billingAddress = $sameAsShipping
            ? $shippingAddress
            : [
                'street' => $validated['billing_street'],
                'city' => $validated['billing_city'],
                'state' => $validated['billing_state'],
                'zip' => $validated['billing_zip'],
                'country' => $validated['billing_country'],
            ];

        return [
            'shipping' => $shippingAddress,
            'billing' => $billingAddress,
        ];
    }
}
