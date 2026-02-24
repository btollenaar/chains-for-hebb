<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryFilterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Product filter by slug includes descendants
     */
    public function test_product_category_filter_includes_descendants(): void
    {
        // Arrange: Create parent + child categories
        $parent = ProductCategory::factory()->create([
            'name' => 'Wellness Supplements',
            'slug' => 'wellness-supplements',
            'is_active' => true,
        ]);

        $child = ProductCategory::factory()->create([
            'name' => 'Vitamins',
            'slug' => 'vitamins',
            'parent_id' => $parent->id,
            'is_active' => true,
        ]);

        // Create product in child category
        $product = Product::factory()->create([
            'name' => 'Vitamin C 1000mg',
            'status' => 'active',
        ]);

        $product->categories()->attach($child->id, [
            'is_primary' => true,
            'display_order' => 1,
        ]);

        // Act: Filter by parent slug
        $response = $this->get('/products?category=wellness-supplements');

        // Assert: Product in child category appears
        $response->assertStatus(200);
        $response->assertSee('Vitamin C 1000mg');
    }

    /**
     * Test: Filter respects active status
     */
    public function test_category_filter_only_returns_active_items(): void
    {
        // Arrange
        $category = ProductCategory::factory()->create([
            'slug' => 'test-category',
            'is_active' => true,
        ]);

        $activeProduct = Product::factory()->create(['status' => 'active']);
        $inactiveProduct = Product::factory()->create(['status' => 'inactive']);

        $activeProduct->categories()->attach($category->id, [
            'is_primary' => true,
            'display_order' => 1,
        ]);
        $inactiveProduct->categories()->attach($category->id, [
            'is_primary' => true,
            'display_order' => 2,
        ]);

        // Act
        $response = $this->get('/products?category=test-category');

        // Assert: Only active product appears
        $response->assertSee($activeProduct->name);
        $response->assertDontSee($inactiveProduct->name);
    }
}
