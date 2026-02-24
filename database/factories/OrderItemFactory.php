<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 5);
        $unitPrice = fake()->randomFloat(2, 10.00, 200.00);
        $subtotal = $unitPrice * $quantity;
        $taxRate = 0.07; // 7% tax
        $taxAmount = round($subtotal * $taxRate, 2);
        $total = $subtotal + $taxAmount;

        return [
            'order_id' => Order::factory(),
            'item_type' => Product::class,  // Default to Product
            'item_id' => Product::factory(),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => $total,
            'attributes' => null,
        ];
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

            $quantity = $attributes['quantity'];
            $unitPrice = $productInstance->current_price;
            $subtotal = $unitPrice * $quantity;
            $taxRate = 0.07;
            $taxAmount = round($subtotal * $taxRate, 2);
            $total = $subtotal + $taxAmount;

            return [
                'item_type' => Product::class,
                'item_id' => $productInstance->id,
                'name' => $productInstance->name,
                'description' => $productInstance->description,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total' => $total,
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

            $quantity = $attributes['quantity'];
            $unitPrice = $serviceInstance->base_price;
            $subtotal = $unitPrice * $quantity;
            $taxRate = 0.07;
            $taxAmount = round($subtotal * $taxRate, 2);
            $total = $subtotal + $taxAmount;

            return [
                'item_type' => Service::class,
                'item_id' => $serviceInstance->id,
                'name' => $serviceInstance->name,
                'description' => $serviceInstance->description,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total' => $total,
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
            'subtotal' => $attributes['unit_price'] * $quantity,
            'tax_amount' => round($attributes['unit_price'] * $quantity * 0.07, 2),
            'total' => ($attributes['unit_price'] * $quantity) + round($attributes['unit_price'] * $quantity * 0.07, 2),
        ]);
    }

    /**
     * Add custom attributes to the item.
     */
    public function withAttributes(array $attributes): static
    {
        return $this->state(fn (array $modelAttributes) => [
            'attributes' => $attributes,
        ]);
    }
}
