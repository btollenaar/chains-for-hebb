<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('????##')),
            'description' => fake()->sentence(),
            'type' => fake()->randomElement(['percentage', 'fixed']),
            'value' => fake()->randomFloat(2, 5, 50),
            'min_order_amount' => null,
            'max_discount_amount' => null,
            'max_uses' => null,
            'used_count' => 0,
            'max_uses_per_customer' => null,
            'starts_at' => null,
            'expires_at' => null,
            'is_active' => true,
        ];
    }

    public function percentage(float $value = 10): static
    {
        return $this->state(fn () => [
            'type' => 'percentage',
            'value' => $value,
        ]);
    }

    public function fixed(float $value = 10): static
    {
        return $this->state(fn () => [
            'type' => 'fixed',
            'value' => $value,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'starts_at' => now()->subMonths(2),
            'expires_at' => now()->subDay(),
        ]);
    }

    public function maxedOut(): static
    {
        return $this->state(fn () => [
            'max_uses' => 5,
            'used_count' => 5,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }

    public function withMinOrder(float $amount = 100): static
    {
        return $this->state(fn () => [
            'min_order_amount' => $amount,
        ]);
    }

    public function withMaxDiscount(float $amount = 50): static
    {
        return $this->state(fn () => [
            'max_discount_amount' => $amount,
        ]);
    }

    public function withMaxUsesPerCustomer(int $max = 1): static
    {
        return $this->state(fn () => [
            'max_uses_per_customer' => $max,
        ]);
    }
}
