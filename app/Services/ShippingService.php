<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ShippingService
{
    /**
     * Calculate total weight of cart items in ounces.
     * Defaults to 8oz per item if weight_oz is not set.
     */
    public function calculateTotalWeight(Collection $cartItems): float
    {
        $totalWeight = 0;

        foreach ($cartItems as $cartItem) {
            $itemWeight = $cartItem->item->weight_oz ?? 8;
            $totalWeight += $itemWeight * $cartItem->quantity;
        }

        return $totalWeight;
    }

    /**
     * Get available shipping rates based on subtotal and weight.
     * Tries Printful rates first for POD items, falls back to hardcoded rates.
     */
    public function getShippingRates(float $subtotal, float $totalWeightOz, ?Collection $cartItems = null, ?array $shippingAddress = null): array
    {
        // Try Printful rates if we have cart items and a shipping address
        if ($cartItems && $shippingAddress) {
            $printfulRates = $this->getPrintfulShippingRates($cartItems, $shippingAddress);
            if (!empty($printfulRates)) {
                return $this->mergeFreeShipping($printfulRates, $subtotal);
            }
        }

        return $this->getHardcodedRates($subtotal, $totalWeightOz);
    }

    /**
     * Get the shipping cost for a specific method.
     */
    public function getShippingCost(string $method, float $subtotal, float $totalWeightOz): float
    {
        $rates = $this->getHardcodedRates($subtotal, $totalWeightOz);

        foreach ($rates as $rate) {
            if ($rate['key'] === $method) {
                return $rate['cost'];
            }
        }

        // Default to standard if method not found
        $standardBase = (float) Setting::get('shipping.standard_base_rate', 5.99);
        $weightLbs = $totalWeightOz / 16;
        $extraWeightLbs = max(0, $weightLbs - 1);

        return round($standardBase + ($extraWeightLbs * 0.50), 2);
    }

    /**
     * Fetch shipping rates from Printful API for cart items.
     */
    private function getPrintfulShippingRates(Collection $cartItems, array $shippingAddress): array
    {
        // Build Printful recipient from shipping address
        $recipient = [
            'address1' => $shippingAddress['street'] ?? $shippingAddress['address1'] ?? '',
            'city' => $shippingAddress['city'] ?? '',
            'state_code' => $shippingAddress['state'] ?? $shippingAddress['state_code'] ?? '',
            'country_code' => $shippingAddress['country'] ?? $shippingAddress['country_code'] ?? 'US',
            'zip' => $shippingAddress['zip'] ?? $shippingAddress['postal_code'] ?? '',
        ];

        // Build Printful items from cart (only Printful-fulfilled products)
        $printfulItems = [];
        foreach ($cartItems as $cartItem) {
            $product = $cartItem->item;
            $variant = $cartItem->variant;

            if (!$product || ($product->fulfillment_type ?? 'printful') !== 'printful') {
                continue;
            }

            $variantId = $variant?->printful_sync_variant_id
                ?? $variant?->printful_variant_id;

            if (!$variantId) {
                continue;
            }

            $printfulItems[] = [
                'variant_id' => $variantId,
                'quantity' => $cartItem->quantity,
            ];
        }

        if (empty($printfulItems) || empty($recipient['zip'])) {
            return [];
        }

        try {
            $printfulService = app(PrintfulService::class);
            $rates = $printfulService->getShippingRates($recipient, $printfulItems);

            return collect($rates)->map(function ($rate) {
                $key = strtolower($rate['id'] ?? $rate['name'] ?? 'standard');

                // Map Printful rate IDs to our keys
                $mappedKey = match (true) {
                    str_contains($key, 'express') || str_contains($key, 'priority') => 'express',
                    str_contains($key, 'standard') || str_contains($key, 'flat') => 'standard',
                    default => $key,
                };

                return [
                    'key' => $mappedKey,
                    'name' => $rate['name'] ?? 'Shipping',
                    'description' => ($rate['minDeliveryDays'] ?? '') && ($rate['maxDeliveryDays'] ?? '')
                        ? "{$rate['minDeliveryDays']}-{$rate['maxDeliveryDays']} business days"
                        : '',
                    'cost' => round((float) ($rate['rate'] ?? 0), 2),
                    'estimated_days' => ($rate['minDeliveryDays'] ?? '') && ($rate['maxDeliveryDays'] ?? '')
                        ? "{$rate['minDeliveryDays']}-{$rate['maxDeliveryDays']}"
                        : '',
                ];
            })->values()->toArray();
        } catch (\Exception $e) {
            Log::warning('Printful shipping rate lookup failed, falling back to hardcoded rates', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Add free shipping option if subtotal exceeds threshold.
     */
    private function mergeFreeShipping(array $rates, float $subtotal): array
    {
        $freeThreshold = (float) Setting::get('shipping.free_threshold', 75.00);

        if ($subtotal >= $freeThreshold) {
            array_unshift($rates, [
                'key' => 'free',
                'name' => 'Free Standard Shipping',
                'description' => '5-7 business days',
                'cost' => 0,
                'estimated_days' => '5-7',
            ]);
        }

        return $rates;
    }

    /**
     * Get hardcoded weight-based shipping rates (fallback).
     */
    private function getHardcodedRates(float $subtotal, float $totalWeightOz): array
    {
        $freeThreshold = (float) Setting::get('shipping.free_threshold', 75.00);
        $standardBase = (float) Setting::get('shipping.standard_base_rate', 5.99);
        $expressBase = (float) Setting::get('shipping.express_base_rate', 12.99);

        $weightLbs = $totalWeightOz / 16;
        $extraWeightLbs = max(0, $weightLbs - 1);

        $standardCost = $standardBase + ($extraWeightLbs * 0.50);
        $expressCost = $expressBase + ($extraWeightLbs * 1.00);

        $methods = [];

        // Free Standard Shipping (above threshold)
        if ($subtotal >= $freeThreshold) {
            $methods[] = [
                'key' => 'free',
                'name' => 'Free Standard Shipping',
                'description' => '5-7 business days',
                'cost' => 0,
                'estimated_days' => '5-7',
            ];
        }

        // Standard Shipping
        $methods[] = [
            'key' => 'standard',
            'name' => 'Standard Shipping',
            'description' => '5-7 business days',
            'cost' => round($standardCost, 2),
            'estimated_days' => '5-7',
        ];

        // Express Shipping
        $methods[] = [
            'key' => 'express',
            'name' => 'Express Shipping',
            'description' => '2-3 business days',
            'cost' => round($expressCost, 2),
            'estimated_days' => '2-3',
        ];

        return $methods;
    }
}
