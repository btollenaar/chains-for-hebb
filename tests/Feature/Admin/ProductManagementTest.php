<?php

namespace Tests\Feature\Admin;

use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    protected Customer $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = Customer::factory()->create([
            'role' => 'admin',
            'is_admin' => true,
        ]);
    }

    /**
     * Test 1: Admin can create product with multiple categories
     * Tests hierarchical category assignment via many-to-many pivot
     */
    public function test_admin_can_create_product_with_categories(): void
    {
        // Arrange
        $this->actingAs($this->admin);

        $category1 = ProductCategory::factory()->create(['name' => 'Category 1']);
        $category2 = ProductCategory::factory()->create(['name' => 'Category 2']);
        $category3 = ProductCategory::factory()->create(['name' => 'Category 3']);

        $productData = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'sku' => 'TEST-SKU-001',
            'price' => 99.99,
            'stock_quantity' => 50,
            'status' => 'active',
            'featured' => true,
            'category_ids' => [$category1->id, $category2->id, $category3->id],
            'primary_category_id' => $category1->id,
        ];

        // Act
        $response = $this->post(route('admin.products.store'), $productData);

        // Assert: Product created
        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'sku' => 'TEST-SKU-001',
            'price' => 99.99,
            'status' => 'active',
            'featured' => true,
        ]);

        $product = Product::where('sku', 'TEST-SKU-001')->first();

        // Assert: Categories attached via pivot
        $this->assertEquals(3, $product->categories()->count());

        // Assert: Primary category set correctly
        $primaryCategory = $product->categories()->wherePivot('is_primary', true)->first();
        $this->assertEquals($category1->id, $primaryCategory->id);

        // Assert: Display order preserved
        $orderedCategories = $product->categories()->orderByPivot('display_order')->get();
        $this->assertEquals($category1->id, $orderedCategories[0]->id);
        $this->assertEquals($category2->id, $orderedCategories[1]->id);
        $this->assertEquals($category3->id, $orderedCategories[2]->id);

        $response->assertRedirect(route('admin.products.index'));
    }

    /**
     * Test 2: Product slug generated automatically
     * Tests SEO-friendly URL generation
     */
    public function test_product_slug_generated_automatically(): void
    {
        // Arrange
        $this->actingAs($this->admin);
        $category = ProductCategory::factory()->create();

        $productData = [
            'name' => 'Amazing Product With Spaces',
            'description' => 'Test',
            'sku' => 'SKU-002',
            'price' => 49.99,
            'stock_quantity' => 100,
            'status' => 'active',
            'category_ids' => [$category->id],
            'primary_category_id' => $category->id,
        ];

        // Act
        $this->post(route('admin.products.store'), $productData);

        // Assert: Slug auto-generated from name
        $this->assertDatabaseHas('products', [
            'name' => 'Amazing Product With Spaces',
            'slug' => 'amazing-product-with-spaces',
        ]);
    }

    /**
     * Test 3: Product images uploaded to storage
     * Tests file upload handling with Storage::fake()
     */
    public function test_product_images_uploaded_to_storage(): void
    {
        // Arrange
        Storage::fake('public');
        $this->actingAs($this->admin);
        $category = ProductCategory::factory()->create();

        $image1 = UploadedFile::fake()->image('product1.jpg', 800, 600);
        $image2 = UploadedFile::fake()->image('product2.jpg', 800, 600);

        $productData = [
            'name' => 'Product with Images',
            'description' => 'Test',
            'sku' => 'SKU-003',
            'price' => 29.99,
            'stock_quantity' => 25,
            'status' => 'active',
            'category_ids' => [$category->id],
            'primary_category_id' => $category->id,
            'images' => [$image1, $image2],
        ];

        // Act
        $this->post(route('admin.products.store'), $productData);

        // Assert: Images stored
        $product = Product::where('sku', 'SKU-003')->first();
        $this->assertNotNull($product->images);
        $this->assertIsArray($product->images);
        $this->assertCount(2, $product->images);

        // Assert: Files exist in storage
        foreach ($product->images as $imagePath) {
            Storage::disk('public')->assertExists($imagePath);
        }
    }

    /**
     * Test 4: XSS sanitization applied to description
     * Tests HTMLPurifier integration for security
     */
    public function test_xss_sanitization_applied_to_description(): void
    {
        // Arrange
        $this->actingAs($this->admin);
        $category = ProductCategory::factory()->create();

        $maliciousHtml = '<p>Safe content</p><script>alert("XSS")</script><img src=x onerror="alert(1)">';

        $productData = [
            'name' => 'Product with XSS',
            'description' => $maliciousHtml,
            'sku' => 'SKU-004',
            'price' => 19.99,
            'stock_quantity' => 10,
            'status' => 'active',
            'category_ids' => [$category->id],
            'primary_category_id' => $category->id,
        ];

        // Act
        $this->post(route('admin.products.store'), $productData);

        // Assert: Script tags removed, safe HTML preserved
        $product = Product::where('sku', 'SKU-004')->first();
        $this->assertStringContainsString('<p>Safe content</p>', $product->description);
        $this->assertStringNotContainsString('<script>', $product->description);
        $this->assertStringNotContainsString('onerror', $product->description);
    }

    /**
     * Test 5: Admin can update product stock
     * Tests inventory management
     */
    public function test_admin_can_update_product_stock(): void
    {
        // Arrange
        $this->actingAs($this->admin);
        $category = ProductCategory::factory()->create();
        $product = Product::factory()->create(['stock_quantity' => 50]);
        $product->categories()->attach($category->id, ['is_primary' => true, 'display_order' => 0]);

        // Act
        $response = $this->put(route('admin.products.update', $product), [
            'name' => $product->name,
            'description' => $product->description,
            'sku' => $product->sku,
            'price' => $product->price,
            'stock_quantity' => 150,
            'status' => $product->status,
            'category_ids' => [$category->id],
            'primary_category_id' => $category->id,
        ]);

        // Assert: Stock updated
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 150,
        ]);

        // Controller redirects to edit page after successful update
        $response->assertRedirect(route('admin.products.edit', $product));
    }

    /**
     * Test 6: Admin can soft delete product
     * Tests soft delete functionality
     */
    public function test_admin_can_soft_delete_product(): void
    {
        // Arrange
        $this->actingAs($this->admin);
        $product = Product::factory()->create();

        // Act
        $response = $this->delete(route('admin.products.destroy', $product));

        // Assert: Product soft deleted
        $this->assertSoftDeleted('products', ['id' => $product->id]);

        // Assert: Product not in active listings
        $this->assertEquals(0, Product::where('id', $product->id)->count());

        // Assert: Product in trashed listings
        $this->assertEquals(1, Product::onlyTrashed()->where('id', $product->id)->count());

        $response->assertRedirect(route('admin.products.index'));
    }

    /**
     * Test 7: Admin can bulk activate products
     * Tests bulk operations
     */
    public function test_admin_can_bulk_activate_products(): void
    {
        // Arrange
        $this->actingAs($this->admin);
        $inactiveProducts = Product::factory()->count(5)->create(['status' => 'inactive']);
        $productIds = $inactiveProducts->pluck('id')->toArray();

        // Act
        $response = $this->post(route('admin.products.bulk'), [
            'action' => 'publish',  // Controller uses 'publish' to activate products
            'ids' => $productIds,   // Controller expects 'ids', not 'product_ids'
        ]);

        // Assert: All products activated
        foreach ($productIds as $id) {
            $this->assertDatabaseHas('products', [
                'id' => $id,
                'status' => 'active',
            ]);
        }

        // Controller uses back() redirect, verify success message
        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /**
     * Test 8: Product category supports multiple assignments
     * Tests many-to-many relationship
     */
    public function test_product_category_supports_multiple(): void
    {
        // Arrange
        $this->actingAs($this->admin);
        $product = Product::factory()->create();
        $categories = ProductCategory::factory()->count(4)->create();

        // Act
        $product->categories()->attach([
            $categories[0]->id => ['is_primary' => true, 'display_order' => 1],
            $categories[1]->id => ['is_primary' => false, 'display_order' => 2],
            $categories[2]->id => ['is_primary' => false, 'display_order' => 3],
            $categories[3]->id => ['is_primary' => false, 'display_order' => 4],
        ]);

        // Assert: All categories attached
        $this->assertEquals(4, $product->categories()->count());

        // Assert: Pivot data preserved
        $pivot = $product->categories()->where('product_categories.id', $categories[0]->id)->first()->pivot;
        $this->assertEquals(1, $pivot->is_primary); // Database stores as 1/0, not true/false
        $this->assertEquals(1, $pivot->display_order);
    }

    /**
     * Test 9: Sale price must be less than regular price
     * Tests business rule validation
     */
    public function test_sale_price_must_be_less_than_regular_price(): void
    {
        // Arrange
        $this->actingAs($this->admin);

        $productData = [
            'name' => 'Invalid Sale Product',
            'description' => 'Test',
            'sku' => 'SKU-INVALID',
            'price' => 50.00,
            'sale_price' => 75.00, // Higher than regular price
            'stock_quantity' => 10,
            'status' => 'active',
        ];

        // Act
        $response = $this->post(route('admin.products.store'), $productData);

        // Assert: Validation error
        $response->assertSessionHasErrors('sale_price');
    }

    /**
     * Test 10: Stock quantity cannot be negative
     * Tests constraint validation
     */
    public function test_stock_quantity_cannot_be_negative(): void
    {
        // Arrange
        $this->actingAs($this->admin);

        $productData = [
            'name' => 'Negative Stock Product',
            'description' => 'Test',
            'sku' => 'SKU-NEG',
            'price' => 30.00,
            'stock_quantity' => -10, // Negative stock
            'status' => 'active',
        ];

        // Act
        $response = $this->post(route('admin.products.store'), $productData);

        // Assert: Validation error
        $response->assertSessionHasErrors('stock_quantity');
    }

    /**
     * Test 11: Product search filters by name and SKU
     * Tests search functionality
     */
    public function test_product_search_filters_by_name_and_sku(): void
    {
        // Arrange
        $this->actingAs($this->admin);
        Product::factory()->create(['name' => 'Apple iPhone 15', 'sku' => 'IPHONE-15']);
        Product::factory()->create(['name' => 'Samsung Galaxy S24', 'sku' => 'GALAXY-S24']);
        Product::factory()->create(['name' => 'Apple MacBook Pro', 'sku' => 'MACBOOK-PRO']);

        // Act: Search by name
        $response = $this->get(route('admin.products.index', ['search' => 'Apple']));

        // Assert: Returns Apple products only
        $response->assertStatus(200);
        $response->assertSee('Apple iPhone 15');
        $response->assertSee('Apple MacBook Pro');
        $response->assertDontSee('Samsung Galaxy S24');

        // Act: Search by SKU
        $response = $this->get(route('admin.products.index', ['search' => 'GALAXY']));

        // Assert: Returns Samsung product
        $response->assertStatus(200);
        $response->assertSee('Samsung Galaxy S24');
        $response->assertDontSee('Apple iPhone 15');
    }

    /**
     * Test 12: Product list eager loads categories
     * Tests N+1 query prevention
     */
    public function test_product_list_eager_loads_categories(): void
    {
        // Arrange
        $this->actingAs($this->admin);

        // Create categories first to avoid slug collisions
        $allCategories = ProductCategory::factory()->count(5)->create();

        $products = Product::factory()->count(10)->create();

        foreach ($products as $product) {
            // Attach random 2 categories from existing pool
            $categories = $allCategories->random(2);
            $product->categories()->attach($categories->pluck('id'));
        }

        // Act & Assert: Count queries
        $queryCount = 0;
        \DB::listen(function () use (&$queryCount) {
            $queryCount++;
        });

        $this->get(route('admin.products.index'));

        // Assert: Should have minimal queries (not 1 + N for categories)
        // Expected: ~8-12 queries (session, auth, products, categories eager load, pagination count, stats, category filter options)
        // Without eager loading this would be 20+ (1 per product for categories)
        $this->assertLessThan(15, $queryCount, "N+1 query detected. Found {$queryCount} queries.");
    }
}
