<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Order;

class CouponService
{
    public function validateCoupon(string $code, float $subtotal, ?int $customerId = null): array
    {
        $coupon = Coupon::where('code', strtoupper($code))->first();

        if (!$coupon) {
            return [
                'valid' => false,
                'error' => 'Invalid coupon code.',
                'discount' => 0,
                'coupon' => null,
                'formatted' => null,
            ];
        }

        $validation = $coupon->validate($subtotal, $customerId);

        if (!$validation['valid']) {
            return [
                'valid' => false,
                'error' => $validation['error'],
                'discount' => 0,
                'coupon' => null,
                'formatted' => null,
            ];
        }

        $discount = $coupon->calculateDiscount($subtotal);

        return [
            'valid' => true,
            'error' => null,
            'discount' => $discount,
            'coupon' => $coupon,
            'formatted' => '-$' . number_format($discount, 2),
        ];
    }

    public function applyCouponToOrder(Order $order, Coupon $coupon, int $customerId): void
    {
        $discount = $coupon->calculateDiscount((float) $order->subtotal);

        $order->update([
            'discount_amount' => $discount,
            'coupon_id' => $coupon->id,
            'coupon_code' => $coupon->code,
            'total_amount' => (float) $order->subtotal + (float) $order->tax_amount - $discount,
        ]);

        $coupon->recordUsage($customerId, $order->id, $discount);
    }
}
