<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'WELCOME10',
                'description' => '10% off for new customers',
                'type' => 'percentage',
                'value' => 10.00,
                'max_uses_per_customer' => 1,
                'is_active' => true,
            ],
            [
                'code' => 'SUMMER20',
                'description' => '20% off summer sale (min $100, max $50 discount)',
                'type' => 'percentage',
                'value' => 20.00,
                'min_order_amount' => 100.00,
                'max_discount_amount' => 50.00,
                'is_active' => true,
            ],
            [
                'code' => 'FLAT50',
                'description' => '$50 off orders over $200',
                'type' => 'fixed',
                'value' => 50.00,
                'min_order_amount' => 200.00,
                'is_active' => true,
            ],
            [
                'code' => 'SAVE15',
                'description' => '15% off any order',
                'type' => 'percentage',
                'value' => 15.00,
                'is_active' => true,
            ],
            [
                'code' => 'VIP25',
                'description' => '25% off for VIP customers (max $75 discount)',
                'type' => 'percentage',
                'value' => 25.00,
                'max_discount_amount' => 75.00,
                'max_uses' => 100,
                'is_active' => true,
            ],
            [
                'code' => 'EXPIRED01',
                'description' => 'Expired promotion',
                'type' => 'percentage',
                'value' => 30.00,
                'starts_at' => now()->subMonths(3),
                'expires_at' => now()->subMonth(),
                'is_active' => true,
            ],
            [
                'code' => 'MAXEDOUT',
                'description' => 'Fully redeemed coupon',
                'type' => 'fixed',
                'value' => 25.00,
                'max_uses' => 10,
                'used_count' => 10,
                'is_active' => true,
            ],
            [
                'code' => 'INACTIVE',
                'description' => 'Deactivated coupon',
                'type' => 'percentage',
                'value' => 50.00,
                'is_active' => false,
            ],
            [
                'code' => 'CART5',
                'description' => '5% off - Abandoned cart recovery discount',
                'type' => 'percentage',
                'value' => 5.00,
                'is_active' => true,
            ],
            [
                'code' => 'LAUNCH15',
                'description' => '15% off - Launch week promotion',
                'type' => 'percentage',
                'value' => 15.00,
                'max_uses_per_customer' => 1,
                'is_active' => true,
            ],
        ];

        foreach ($coupons as $coupon) {
            Coupon::create($coupon);
        }
    }
}
