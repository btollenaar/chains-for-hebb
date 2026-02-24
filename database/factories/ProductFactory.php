<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'slug' => null,  // Auto-generated in model boot
            'description' => fake()->paragraph(),
            'long_description' => fake()->paragraphs(3, true),
            'sku' => strtoupper(fake()->unique()->bothify('SKU-####??')),
            'barcode' => fake()->boolean(30) ? fake()->ean13() : null,
            'price' => fake()->randomFloat(2, 9.99, 299.99),
            'sale_price' => null,
            'cost' => fake()->randomFloat(2, 5.00, 150.00),
            'stock_quantity' => fake()->numberBetween(0, 200),
            'low_stock_threshold' => 5,
            'category' => null, // Legacy field, use category_id instead
            'category_id' => ProductCategory::factory(),
            'subcategory' => null, // Legacy field
            'tags' => fake()->boolean(50) ? fake()->words(3) : null,
            'attributes' => null,
            'featured' => fake()->boolean(20), // 20% chance of being featured
            'status' => 'active',
            'meta_title' => null,
            'meta_description' => null,
            'images' => [],
        ];
    }

    /**
     * Indicate that the product is in stock with a specific quantity.
     */
    public function inStock(int $quantity = 50): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => $quantity,
        ]);
    }

    /**
     * Indicate that the product is on sale.
     */
    public function onSale(): static
    {
        return $this->state(fn (array $attributes) => [
            'sale_price' => $attributes['price'] * 0.8, // 20% discount
        ]);
    }

    /**
     * Indicate that the product is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => 0,
        ]);
    }

    /**
     * Indicate that the product is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'featured' => true,
        ]);
    }

    /**
     * Indicate that the product is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Indicate that the product is low on stock.
     */
    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => fake()->numberBetween(1, 5),
        ]);
    }

    /**
     * Add multiple categories to the product via pivot table after creation.
     */
    public function withCategories(int $count = 3): static
    {
        return $this->afterCreating(function (Product $product) use ($count) {
            $categories = ProductCategory::factory()->count($count)->create();

            $product->categories()->attach(
                $categories->mapWithKeys(function ($cat, $index) {
                    return [$cat->id => [
                        'is_primary' => $index === 0,
                        'display_order' => $index + 1
                    ]];
                })
            );
        });
    }

    /**
     * Add product images.
     */
    public function withImages(int $count = 3): static
    {
        return $this->state(fn (array $attributes) => [
            'images' => collect(range(1, $count))->map(fn($i) =>
                'products/product-' . fake()->uuid() . '.jpg'
            )->toArray(),
        ]);
    }

    /**
     * Set complete SEO metadata.
     */
    public function withSeo(): static
    {
        return $this->state(fn (array $attributes) => [
            'meta_title' => $attributes['name'] . ' - Buy Online',
            'meta_description' => fake()->sentence(20),
        ]);
    }

    /**
     * Set custom product attributes (e.g., size, color).
     */
    public function withAttributes(array $attributes): static
    {
        return $this->state(fn (array $modelAttributes) => [
            'attributes' => $attributes,
        ]);
    }
}
