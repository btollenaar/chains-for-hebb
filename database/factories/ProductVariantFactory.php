<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition(): array
    {
        $colors = ['Black', 'White', 'Navy', 'Red', 'Heather Grey', 'Forest Green'];
        $sizes = ['S', 'M', 'L', 'XL', '2XL'];

        $cost = fake()->randomFloat(2, 8.00, 25.00);

        return [
            'product_id' => Product::factory(),
            'printful_variant_id' => fake()->unique()->numberBetween(1000, 99999),
            'printful_sync_variant_id' => fake()->unique()->numberBetween(100000, 999999),
            'color_name' => fake()->randomElement($colors),
            'color_hex' => fake()->hexColor(),
            'size' => fake()->randomElement($sizes),
            'sku' => strtoupper(fake()->unique()->bothify('PV-####??')),
            'printful_cost' => $cost,
            'retail_price' => round($cost * fake()->randomFloat(2, 1.8, 3.0), 2),
            'is_active' => true,
            'stock_status' => 'in_stock',
            'sort_order' => 0,
        ];
    }

    public function outOfStock(): static
    {
        return $this->state(fn () => ['stock_status' => 'out_of_stock']);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function forProduct(Product $product): static
    {
        return $this->state(fn () => ['product_id' => $product->id]);
    }
}
