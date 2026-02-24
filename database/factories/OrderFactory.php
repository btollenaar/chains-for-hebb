<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 50.00, 500.00);
        $taxRate = 0.07; // 7% tax
        $taxAmount = $subtotal * $taxRate;
        $discountAmount = 0;
        $totalAmount = $subtotal + $taxAmount - $discountAmount;

        return [
            'customer_id' => Customer::factory(),
            'order_number' => null,  // Auto-generated in model boot
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
            'payment_method' => fake()->randomElement(['stripe', 'cash', 'check']),
            'payment_status' => 'pending',
            'payment_intent_id' => null,
            'stripe_session_id' => null,
            'stripe_payment_intent_id' => null,
            'fulfillment_status' => 'pending',
            'billing_address' => $this->generateAddress(),
            'shipping_address' => $this->generateAddress(),
            'notes' => fake()->boolean(30) ? fake()->sentence() : null,
            'admin_notes' => null,
        ];
    }

    /**
     * Indicate that the order is paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'paid',
            'stripe_payment_intent_id' => 'pi_' . fake()->bothify('??##################'),
        ]);
    }

    /**
     * Indicate that the order is pending payment.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'pending',
        ]);
    }

    /**
     * Indicate that the order payment failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'failed',
        ]);
    }

    /**
     * Indicate that the order is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'fulfillment_status' => 'completed',
            'payment_status' => 'paid',
        ]);
    }

    /**
     * Indicate that the order is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'fulfillment_status' => 'cancelled',
        ]);
    }

    /**
     * Indicate that the order is processing.
     */
    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'fulfillment_status' => 'processing',
            'payment_status' => 'paid',
        ]);
    }

    /**
     * Set payment method to Stripe.
     */
    public function stripe(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'stripe',
            'stripe_session_id' => 'cs_test_' . fake()->bothify('??????????????????????????????????'),
            'stripe_payment_intent_id' => 'pi_' . fake()->bothify('??##################'),
        ]);
    }

    /**
     * Set payment method to cash.
     */
    public function cash(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'cash',
        ]);
    }

    /**
     * Set payment method to check.
     */
    public function check(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'check',
        ]);
    }

    /**
     * Add a discount to the order.
     */
    public function withDiscount(float $amount): static
    {
        return $this->state(fn (array $attributes) => [
            'discount_amount' => $amount,
            'total_amount' => $attributes['subtotal'] + $attributes['tax_amount'] - $amount,
        ]);
    }

    /**
     * Create order with associated order items.
     */
    public function withItems(int $count = 2): static
    {
        return $this->afterCreating(function (Order $order) use ($count) {
            OrderItem::factory()
                ->count($count)
                ->create(['order_id' => $order->id]);
        });
    }

    /**
     * Add admin notes to the order.
     */
    public function withAdminNotes(string $notes): static
    {
        return $this->state(fn (array $attributes) => [
            'admin_notes' => $notes,
        ]);
    }

    /**
     * Generate a realistic address array.
     */
    private function generateAddress(): array
    {
        return [
            'name' => fake()->name(),
            'street' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->stateAbbr(),
            'zip' => fake()->postcode(),
            'country' => 'USA',
            'phone' => fake()->phoneNumber(),
        ];
    }
}
