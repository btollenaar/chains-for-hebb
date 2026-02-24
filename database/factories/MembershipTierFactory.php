<?php

namespace Database\Factories;

use App\Models\MembershipTier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MembershipTierFactory extends Factory
{
    protected $model = MembershipTier::class;

    public function definition(): array
    {
        $name = fake()->unique()->randomElement(['Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond', 'Elite', 'Premium', 'VIP']);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 5, 99),
            'billing_interval' => 'monthly',
            'discount_percentage' => fake()->randomElement([5, 10, 15, 20]),
            'features' => [fake()->sentence(), fake()->sentence()],
            'priority_booking' => false,
            'free_shipping' => false,
            'is_active' => true,
            'display_order' => 0,
            'badge_color' => fake()->hexColor(),
        ];
    }

    public function yearly(): static
    {
        return $this->state(fn() => [
            'billing_interval' => 'yearly',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn() => [
            'is_active' => false,
        ]);
    }

    public function withPriorityBooking(): static
    {
        return $this->state(fn() => [
            'priority_booking' => true,
        ]);
    }

    public function withFreeShipping(): static
    {
        return $this->state(fn() => [
            'free_shipping' => true,
        ]);
    }

    public function premium(): static
    {
        return $this->state(fn() => [
            'price' => 49.99,
            'discount_percentage' => 15,
            'priority_booking' => true,
            'free_shipping' => true,
        ]);
    }
}
