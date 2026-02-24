<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductFilterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper: create an active, in-stock product with specific variants.
     *
     * @param  string  $name
     * @param  array   $variants  Each element is an array with keys: color_name, size, retail_price, stock_status
     * @param  array   $productOverrides  Extra product attributes
     * @return Product
     */
    private function createProductWithVariants(string $name, array $variants, array $productOverrides = []): Product
    {
        $product = Product::factory()->create(array_merge([
            'name' => $name,
            'status' => 'active',
            'stock_quantity' => 50,
        ], $productOverrides));

        foreach ($variants as $variant) {
            ProductVariant::factory()->create(array_merge([
                'product_id' => $product->id,
                'is_active' => true,
                'stock_status' => 'in_stock',
            ], $variant));
        }

        return $product;
    }

    // ---------------------------------------------------------------
    // Test 1: Products page loads with filter options
    // ---------------------------------------------------------------

    public function test_products_page_loads_with_filter_options(): void
    {
        // Arrange: create a product with a variant so filter options are populated
        $this->createProductWithVariants('Filter Test Shirt', [
            ['color_name' => 'Black', 'color_hex' => '#000000', 'size' => 'M', 'retail_price' => 24.99],
        ]);

        // Act
        $response = $this->get(route('products.index'));

        // Assert: page loads successfully and contains filter-related UI elements
        $response->assertStatus(200);
        $response->assertSee('Filter Test Shirt');
        // The view renders filter options when $filterOptions is set
        $response->assertSee('Colors');
        $response->assertSee('Black');
    }

    // ---------------------------------------------------------------
    // Test 2: Filtering by color returns only matching products
    // ---------------------------------------------------------------

    public function test_filter_by_color_returns_matching_products(): void
    {
        // Arrange
        $this->createProductWithVariants('Red Hoodie', [
            ['color_name' => 'Red', 'color_hex' => '#FF0000', 'size' => 'L', 'retail_price' => 39.99],
        ]);

        $this->createProductWithVariants('Blue T-Shirt', [
            ['color_name' => 'Blue', 'color_hex' => '#0000FF', 'size' => 'M', 'retail_price' => 24.99],
        ]);

        // Act: filter by color = Red
        $response = $this->get(route('products.index', ['colors' => ['Red']]));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('Red Hoodie');
        $response->assertDontSee('Blue T-Shirt');
    }

    // ---------------------------------------------------------------
    // Test 3: Filtering by size returns only matching products
    // ---------------------------------------------------------------

    public function test_filter_by_size_returns_matching_products(): void
    {
        // Arrange
        $this->createProductWithVariants('Small Cap', [
            ['color_name' => 'Black', 'color_hex' => '#000000', 'size' => 'S', 'retail_price' => 19.99],
        ]);

        $this->createProductWithVariants('XL Jacket', [
            ['color_name' => 'Black', 'color_hex' => '#000000', 'size' => 'XL', 'retail_price' => 59.99],
        ]);

        // Act: filter by size = S
        $response = $this->get(route('products.index', ['sizes' => ['S']]));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('Small Cap');
        $response->assertDontSee('XL Jacket');
    }

    // ---------------------------------------------------------------
    // Test 4: Price range filter returns products within range
    // ---------------------------------------------------------------

    public function test_price_range_filter_returns_products_within_range(): void
    {
        // Arrange
        $this->createProductWithVariants('Budget Sticker', [
            ['color_name' => 'White', 'color_hex' => '#FFFFFF', 'size' => null, 'retail_price' => 5.00],
        ]);

        $this->createProductWithVariants('Mid Range Mug', [
            ['color_name' => 'White', 'color_hex' => '#FFFFFF', 'size' => null, 'retail_price' => 25.00],
        ]);

        $this->createProductWithVariants('Premium Hoodie', [
            ['color_name' => 'Black', 'color_hex' => '#000000', 'size' => 'L', 'retail_price' => 65.00],
        ]);

        // Act: filter by price range 10–30
        $response = $this->get(route('products.index', [
            'min_price' => 10,
            'max_price' => 30,
        ]));

        // Assert: only the mid-range product appears
        $response->assertStatus(200);
        $response->assertSee('Mid Range Mug');
        $response->assertDontSee('Budget Sticker');
        $response->assertDontSee('Premium Hoodie');
    }

    // ---------------------------------------------------------------
    // Test 5: In-stock filter works correctly
    // ---------------------------------------------------------------

    public function test_in_stock_filter_excludes_out_of_stock_variants(): void
    {
        // Arrange: product whose only variant is out of stock
        $this->createProductWithVariants('Out Of Stock Poster', [
            ['color_name' => 'White', 'color_hex' => '#FFFFFF', 'size' => null, 'retail_price' => 15.00, 'stock_status' => 'out_of_stock'],
        ]);

        // Arrange: product with an in-stock variant
        $this->createProductWithVariants('Available Tee', [
            ['color_name' => 'Black', 'color_hex' => '#000000', 'size' => 'M', 'retail_price' => 24.99, 'stock_status' => 'in_stock'],
        ]);

        // Act: filter by in_stock=1
        $response = $this->get(route('products.index', ['in_stock' => 1]));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('Available Tee');
        $response->assertDontSee('Out Of Stock Poster');
    }

    // ---------------------------------------------------------------
    // Test 6: Multiple filters can be combined
    // ---------------------------------------------------------------

    public function test_multiple_filters_can_be_combined(): void
    {
        // Arrange: Black / M / $25 — matches all filters
        $this->createProductWithVariants('Perfect Match Tee', [
            ['color_name' => 'Black', 'color_hex' => '#000000', 'size' => 'M', 'retail_price' => 25.00],
        ]);

        // Arrange: Black / XL / $25 — wrong size
        $this->createProductWithVariants('Wrong Size Tee', [
            ['color_name' => 'Black', 'color_hex' => '#000000', 'size' => 'XL', 'retail_price' => 25.00],
        ]);

        // Arrange: Red / M / $25 — wrong color
        $this->createProductWithVariants('Wrong Color Tee', [
            ['color_name' => 'Red', 'color_hex' => '#FF0000', 'size' => 'M', 'retail_price' => 25.00],
        ]);

        // Arrange: Black / M / $60 — over price cap
        $this->createProductWithVariants('Too Expensive Tee', [
            ['color_name' => 'Black', 'color_hex' => '#000000', 'size' => 'M', 'retail_price' => 60.00],
        ]);

        // Act: combine color + size + price
        $response = $this->get(route('products.index', [
            'colors' => ['Black'],
            'sizes' => ['M'],
            'min_price' => 20,
            'max_price' => 30,
        ]));

        // Assert: only the perfect match appears
        $response->assertStatus(200);
        $response->assertSee('Perfect Match Tee');
        $response->assertDontSee('Wrong Size Tee');
        $response->assertDontSee('Wrong Color Tee');
        $response->assertDontSee('Too Expensive Tee');
    }

    // ---------------------------------------------------------------
    // Test 7: Category page also supports filters
    // ---------------------------------------------------------------

    public function test_category_page_supports_filters(): void
    {
        // Arrange: create a category and two products in it
        $category = ProductCategory::factory()->create([
            'name' => 'Apparel',
            'slug' => 'apparel',
            'is_active' => true,
        ]);

        $redProduct = $this->createProductWithVariants('Red Apparel Shirt', [
            ['color_name' => 'Red', 'color_hex' => '#FF0000', 'size' => 'M', 'retail_price' => 30.00],
        ], ['category_id' => $category->id]);
        $redProduct->categories()->attach($category->id, ['is_primary' => true, 'display_order' => 1]);

        $blueProduct = $this->createProductWithVariants('Blue Apparel Shirt', [
            ['color_name' => 'Blue', 'color_hex' => '#0000FF', 'size' => 'L', 'retail_price' => 35.00],
        ], ['category_id' => $category->id]);
        $blueProduct->categories()->attach($category->id, ['is_primary' => true, 'display_order' => 2]);

        // Act: visit category page with color filter
        $response = $this->get(route('products.category', ['category' => 'apparel', 'colors' => ['Red']]));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('Red Apparel Shirt');
        $response->assertDontSee('Blue Apparel Shirt');
    }

    // ---------------------------------------------------------------
    // Test 8: Search finds products by name
    // ---------------------------------------------------------------

    public function test_search_finds_products_by_name(): void
    {
        // Arrange
        $this->createProductWithVariants('Galaxy Print Hoodie', [
            ['color_name' => 'Black', 'color_hex' => '#000000', 'size' => 'L', 'retail_price' => 45.00],
        ]);

        $this->createProductWithVariants('Mountain Landscape Mug', [
            ['color_name' => 'White', 'color_hex' => '#FFFFFF', 'size' => null, 'retail_price' => 18.00],
        ]);

        // Act: search for "Galaxy"
        $response = $this->get(route('products.index', ['search' => 'Galaxy']));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('Galaxy Print Hoodie');
        $response->assertDontSee('Mountain Landscape Mug');
    }

    // ---------------------------------------------------------------
    // Test 9: Inactive products are not shown on storefront
    // ---------------------------------------------------------------

    public function test_inactive_products_do_not_appear(): void
    {
        // Arrange: active product
        $this->createProductWithVariants('Visible Tee', [
            ['color_name' => 'Black', 'color_hex' => '#000000', 'size' => 'M', 'retail_price' => 20.00],
        ]);

        // Arrange: inactive product
        $this->createProductWithVariants('Hidden Tee', [
            ['color_name' => 'Black', 'color_hex' => '#000000', 'size' => 'M', 'retail_price' => 20.00],
        ], ['status' => 'inactive']);

        // Act
        $response = $this->get(route('products.index'));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('Visible Tee');
        $response->assertDontSee('Hidden Tee');
    }

    // ---------------------------------------------------------------
    // Test 10: Out-of-stock products (0 stock_quantity) are excluded
    // ---------------------------------------------------------------

    public function test_out_of_stock_products_are_excluded(): void
    {
        // Arrange: in-stock product
        $this->createProductWithVariants('Stocked Item', [
            ['color_name' => 'White', 'color_hex' => '#FFFFFF', 'size' => 'M', 'retail_price' => 20.00],
        ], ['stock_quantity' => 50]);

        // Arrange: product with zero stock_quantity (excluded by inStock scope)
        $this->createProductWithVariants('Zero Stock Item', [
            ['color_name' => 'White', 'color_hex' => '#FFFFFF', 'size' => 'M', 'retail_price' => 20.00],
        ], ['stock_quantity' => 0]);

        // Act
        $response = $this->get(route('products.index'));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('Stocked Item');
        $response->assertDontSee('Zero Stock Item');
    }
}
