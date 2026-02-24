<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\LoyaltyPoint;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class LoyaltyService
{
    /**
     * Earn loyalty points for a customer.
     */
    public function earnPoints(Customer $customer, int $points, string $source, ?int $sourceId = null, string $description = ''): LoyaltyPoint
    {
        return DB::transaction(function () use ($customer, $points, $source, $sourceId, $description) {
            $customer->increment('loyalty_points_balance', $points);
            $customer->refresh();

            return LoyaltyPoint::create([
                'customer_id' => $customer->id,
                'points' => $points,
                'type' => 'earned',
                'source' => $source,
                'source_id' => $sourceId,
                'description' => $description,
                'balance_after' => $customer->loyalty_points_balance,
            ]);
        });
    }

    /**
     * Redeem loyalty points against an order.
     */
    public function redeemPoints(Customer $customer, int $points, Order $order): LoyaltyPoint
    {
        if ($points > $customer->loyalty_points_balance) {
            throw new \InvalidArgumentException('Insufficient loyalty points balance.');
        }

        if ($points <= 0) {
            throw new \InvalidArgumentException('Points must be greater than zero.');
        }

        return DB::transaction(function () use ($customer, $points, $order) {
            $customer->decrement('loyalty_points_balance', $points);
            $customer->refresh();

            $discount = $this->calculateDiscountFromPoints($points);
            $order->update([
                'loyalty_points_redeemed' => $points,
                'loyalty_discount' => $discount,
            ]);

            return LoyaltyPoint::create([
                'customer_id' => $customer->id,
                'points' => -$points,
                'type' => 'redeemed',
                'source' => 'order',
                'source_id' => $order->id,
                'description' => "Redeemed {$points} points for \${$discount} discount on Order #{$order->order_number}",
                'balance_after' => $customer->loyalty_points_balance,
            ]);
        });
    }

    /**
     * Admin adjustment of loyalty points.
     */
    public function adjustPoints(Customer $customer, int $points, string $description = 'Admin adjustment'): LoyaltyPoint
    {
        return DB::transaction(function () use ($customer, $points, $description) {
            $customer->increment('loyalty_points_balance', $points);
            $customer->refresh();

            return LoyaltyPoint::create([
                'customer_id' => $customer->id,
                'points' => $points,
                'type' => 'adjusted',
                'source' => 'admin',
                'description' => $description,
                'balance_after' => $customer->loyalty_points_balance,
            ]);
        });
    }

    /**
     * Calculate how many points an order earns.
     */
    public function calculatePointsForOrder(Order $order): int
    {
        $rate = (int) Setting::get('loyalty.points_per_dollar', 1);
        return (int) floor($order->subtotal * $rate);
    }

    /**
     * Convert points to dollar discount.
     */
    public function calculateDiscountFromPoints(int $points): float
    {
        $rate = (int) Setting::get('loyalty.points_per_dollar_discount', 100);
        return round($points / $rate, 2);
    }

    /**
     * Get the maximum redeemable points for a given order total.
     * Caps at 50% of order total.
     */
    public function getMaxRedeemablePoints(Customer $customer, float $orderTotal): int
    {
        $maxDiscount = $orderTotal * 0.5; // Max 50% of order total
        $rate = (int) Setting::get('loyalty.points_per_dollar_discount', 100);
        $maxPointsForDiscount = (int) floor($maxDiscount * $rate);

        return min($customer->loyalty_points_balance, $maxPointsForDiscount);
    }
}
