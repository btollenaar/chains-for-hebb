<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Membership;
use App\Models\MembershipTier;
use Illuminate\Database\Eloquent\Factories\Factory;

class MembershipFactory extends Factory
{
    protected $model = Membership::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'membership_tier_id' => MembershipTier::factory(),
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
        ];
    }

    public function active(): static
    {
        return $this->state(fn() => [
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn() => [
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn() => [
            'status' => 'expired',
            'starts_at' => now()->subMonths(2),
            'expires_at' => now()->subMonth(),
        ]);
    }

    public function pastDue(): static
    {
        return $this->state(fn() => [
            'status' => 'past_due',
        ]);
    }

    public function withStripe(): static
    {
        return $this->state(fn() => [
            'stripe_subscription_id' => 'sub_' . fake()->unique()->regexify('[A-Za-z0-9]{24}'),
        ]);
    }

    public function forCustomer(Customer $customer): static
    {
        return $this->state(fn() => [
            'customer_id' => $customer->id,
        ]);
    }

    public function forTier(MembershipTier $tier): static
    {
        return $this->state(fn() => [
            'membership_tier_id' => $tier->id,
        ]);
    }
}
