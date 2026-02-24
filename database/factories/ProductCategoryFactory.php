<?php

namespace Database\Factories;

use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductCategory>
 */
class ProductCategoryFactory extends Factory
{
    protected $model = ProductCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'slug' => null,  // Auto-generated in model boot
            'description' => fake()->sentence(),
            'image' => null,
            'display_order' => fake()->numberBetween(1, 100),
            'is_active' => true,
            'parent_id' => null,
        ];
    }

    /**
     * Indicate that the category is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the category is a child of another category.
     */
    public function childOf(ProductCategory|int $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => is_int($parent) ? $parent : $parent->id,
        ]);
    }

    /**
     * Create a category with children.
     */
    public function withChildren(int $count = 3): static
    {
        return $this->afterCreating(function (ProductCategory $category) use ($count) {
            ProductCategory::factory()
                ->count($count)
                ->childOf($category)
                ->create();
        });
    }

    /**
     * Create a multi-level category tree.
     *
     * @param int $levels Number of levels deep
     * @param int $childrenPerLevel Number of children at each level
     */
    public function tree(int $levels = 3, int $childrenPerLevel = 3): static
    {
        return $this->afterCreating(function (ProductCategory $category) use ($levels, $childrenPerLevel) {
            $this->createTreeLevel($category, $levels - 1, $childrenPerLevel);
        });
    }

    /**
     * Recursively create category tree levels.
     */
    private function createTreeLevel(ProductCategory $parent, int $remainingLevels, int $childrenCount): void
    {
        if ($remainingLevels <= 0) {
            return;
        }

        $children = ProductCategory::factory()
            ->count($childrenCount)
            ->childOf($parent)
            ->create();

        // Recursively create children for each child
        $children->each(function ($child) use ($remainingLevels, $childrenCount) {
            $this->createTreeLevel($child, $remainingLevels - 1, $childrenCount);
        });
    }

    /**
     * Set a specific display order.
     */
    public function order(int $order): static
    {
        return $this->state(fn (array $attributes) => [
            'display_order' => $order,
        ]);
    }

    /**
     * Add an image to the category.
     */
    public function withImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'image' => 'categories/category-' . fake()->uuid() . '.jpg',
        ]);
    }
}
