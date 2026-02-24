<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cart>
 */
class CartFactory extends Factory
{
    protected $model = Cart::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'session_id' => null,
            'item_type' => Product::class,  // Default to Product
            'item_id' => Product::factory(),
            'quantity' => fake()->numberBetween(1, 5),
            'attributes' => null,
        ];
    }

    /**
     * Indicate that the cart item is for a guest (session-based).
     */
    public function guest(): static
    {
        return $this->state(fn (array $attributes) => [
            'customer_id' => null,
            'session_id' => fake()->uuid(),
        ]);
    }

    /**
     * Indicate that the cart item is for an authenticated customer.
     */
    public function authenticated(Customer|int $customer): static
    {
        return $this->state(fn (array $attributes) => [
            'customer_id' => $customer instanceof Customer ? $customer->id : $customer,
            'session_id' => null,
        ]);
    }

    /**
     * Indicate that the item is a product.
     */
    public function forProduct(Product|int|null $product = null): static
    {
        return $this->state(function (array $attributes) use ($product) {
            $productInstance = $product instanceof Product
                ? $product
                : Product::factory()->create();

            return [
                'item_type' => Product::class,
                'item_id' => $productInstance->id,
            ];
        });
    }

    /**
     * Indicate that the item is a service.
     */
    public function forService(Service|int|null $service = null): static
    {
        return $this->state(function (array $attributes) use ($service) {
            $serviceInstance = $service instanceof Service
                ? $service
                : Service::factory()->create();

            return [
                'item_type' => Service::class,
                'item_id' => $serviceInstance->id,
            ];
        });
    }

    /**
     * Set a specific quantity.
     */
    public function quantity(int $quantity): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity' => $quantity,
        ]);
    }

    /**
     * Add custom attributes to the cart item.
     */
    public function withAttributes(array $attributes): static
    {
        return $this->state(fn (array $modelAttributes) => [
            'attributes' => $attributes,
        ]);
    }
}
