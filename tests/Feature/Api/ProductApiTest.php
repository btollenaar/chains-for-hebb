<?php

namespace Tests\Feature\Api;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_products(): void
    {
        Product::factory()->count(3)->create(['status' => 'active']);

        $response = $this->getJson('/api/v1/products');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'name', 'price', 'in_stock']],
                'meta' => ['total', 'per_page'],
            ]);
    }

    public function test_show_product(): void
    {
        $product = Product::factory()->create(['status' => 'active']);

        $response = $this->getJson("/api/v1/products/{$product->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $product->id)
            ->assertJsonPath('data.name', $product->name);
    }

    public function test_inactive_product_returns_404(): void
    {
        $product = Product::factory()->create(['status' => 'inactive']);

        $response = $this->getJson("/api/v1/products/{$product->id}");
        $response->assertNotFound();
    }

    public function test_list_products_with_search(): void
    {
        Product::factory()->create(['name' => 'Classic Logo Hoodie', 'status' => 'active']);
        Product::factory()->create(['name' => 'Vintage Poster Print', 'status' => 'active']);

        $response = $this->getJson('/api/v1/products?search=Hoodie');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Classic Logo Hoodie', $response->json('data.0.name'));
    }

    public function test_list_products_with_category_filter(): void
    {
        $category = ProductCategory::factory()->create();
        Product::factory()->count(2)->create(['category_id' => $category->id, 'status' => 'active']);
        Product::factory()->create(['status' => 'active']);

        $response = $this->getJson("/api/v1/products?category={$category->id}");

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
    }

    public function test_list_products_with_price_filter(): void
    {
        Product::factory()->create(['price' => 10.00, 'status' => 'active']);
        Product::factory()->create(['price' => 50.00, 'status' => 'active']);
        Product::factory()->create(['price' => 100.00, 'status' => 'active']);

        $response = $this->getJson('/api/v1/products?min_price=20&max_price=60');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    public function test_list_products_with_sorting(): void
    {
        Product::factory()->create(['name' => 'Zulu Product', 'status' => 'active']);
        Product::factory()->create(['name' => 'Alpha Product', 'status' => 'active']);

        $response = $this->getJson('/api/v1/products?sort=name&direction=asc');

        $response->assertOk();
        $this->assertEquals('Alpha Product', $response->json('data.0.name'));
        $this->assertEquals('Zulu Product', $response->json('data.1.name'));
    }

    public function test_list_products_per_page_capped_at_100(): void
    {
        Product::factory()->count(5)->create(['status' => 'active']);

        $response = $this->getJson('/api/v1/products?per_page=200');

        $response->assertOk();
        $perPage = $response->json('meta.per_page');
        // Handle both array (custom collection wrapping) and scalar values
        $perPageValue = is_array($perPage) ? $perPage[0] : $perPage;
        $this->assertLessThanOrEqual(100, $perPageValue);
    }

    public function test_list_products_excludes_inactive(): void
    {
        Product::factory()->create(['status' => 'active']);
        Product::factory()->create(['status' => 'inactive']);

        $response = $this->getJson('/api/v1/products');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    public function test_show_product_includes_category(): void
    {
        $category = ProductCategory::factory()->create(['name' => 'Eco Kitchen']);
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'status' => 'active',
        ]);

        $response = $this->getJson("/api/v1/products/{$product->id}");

        $response->assertOk()
            ->assertJsonPath('data.category.name', 'Eco Kitchen');
    }

    public function test_nonexistent_product_returns_404(): void
    {
        $response = $this->getJson('/api/v1/products/99999');
        $response->assertNotFound();
    }
}
