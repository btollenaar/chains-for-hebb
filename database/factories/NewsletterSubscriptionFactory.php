<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\NewsletterSubscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NewsletterSubscription>
 */
class NewsletterSubscriptionFactory extends Factory
{
    protected $model = NewsletterSubscription::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'name' => fake()->name(),
            'customer_id' => null,
            'source' => fake()->randomElement(['signup_form', 'checkout', 'manual']),
            'is_active' => true,
            'subscribed_at' => now()->subDays(fake()->numberBetween(1, 365)),
            'unsubscribed_at' => null,
        ];
    }

    /**
     * Indicate that the subscription is from a customer.
     */
    public function fromCustomer(Customer|int|null $customer = null): static
    {
        return $this->state(function (array $attributes) use ($customer) {
            $customerInstance = $customer instanceof Customer
                ? $customer
                : Customer::factory()->create();

            return [
                'customer_id' => $customerInstance->id,
                'email' => $customerInstance->email,
                'name' => $customerInstance->name,
            ];
        });
    }

    /**
     * Indicate that the subscription is from checkout.
     */
    public function fromCheckout(): static
    {
        return $this->state(fn (array $attributes) => [
            'source' => 'checkout',
        ]);
    }

    /**
     * Indicate that the subscription is from a signup form.
     */
    public function fromSignupForm(): static
    {
        return $this->state(fn (array $attributes) => [
            'source' => 'signup_form',
        ]);
    }

    /**
     * Indicate that the subscription was manually added.
     */
    public function manual(): static
    {
        return $this->state(fn (array $attributes) => [
            'source' => 'manual',
        ]);
    }

    /**
     * Indicate that the subscription is inactive (unsubscribed).
     */
    public function unsubscribed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
            'unsubscribed_at' => now()->subDays(fake()->numberBetween(1, 90)),
        ]);
    }

    /**
     * Indicate that the subscription is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'unsubscribed_at' => null,
        ]);
    }
}
