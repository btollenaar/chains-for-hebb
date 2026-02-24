<?php

namespace Tests\Browser;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SearchFunctionalityTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test 1: Search bar returns results
     * Tests: Search input, form submission, results display, relevance
     */
    public function test_search_returns_results(): void
    {
        $category = ProductCategory::factory()->create();

        // Create test products with distinct names
        $product1 = Product::factory()->create([
            'name' => 'Unique Search Product Alpha',
            'sku' => 'USP-ALPHA',
            'price' => 49.99,
            'stock_quantity' => 100,
            'status' => 'active',
        ]);
        $product1->categories()->attach($category->id, ['is_primary' => true, 'display_order' => 1]);

        $product2 = Product::factory()->create([
            'name' => 'Unique Search Product Beta',
            'sku' => 'USP-BETA',
            'price' => 79.99,
            'stock_quantity' => 50,
            'status' => 'active',
        ]);
        $product2->categories()->attach($category->id, ['is_primary' => true, 'display_order' => 1]);

        $product3 = Product::factory()->create([
            'name' => 'Different Product Gamma',
            'sku' => 'DIF-GAMMA',
            'price' => 99.99,
            'stock_quantity' => 25,
            'status' => 'active',
        ]);
        $product3->categories()->attach($category->id, ['is_primary' => true, 'display_order' => 1]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/products')

                    // Verify all products initially visible
                    ->assertSee('Unique Search Product Alpha')
                    ->assertSee('Unique Search Product Beta')
                    ->assertSee('Different Product Gamma')

                    // Locate search input
                    ->assertPresent('input[name="search"], input[type="search"]')

                    // Type search query
                    ->type('input[name="search"], input[type="search"]', 'Unique Search')
                    ->pause(500)

                    // Submit search (press Enter or click search button)
                    ->keys('input[name="search"], input[type="search"]', '{enter}')
                    ->pause(1000) // Wait for results

                    // Verify filtered results
                    ->assertSee('Unique Search Product Alpha')
                    ->assertSee('Unique Search Product Beta')
                    ->assertDontSee('Different Product Gamma') // Not matching search

                    // Verify result count or message
                    ->assertSee('2'); // Result count indicator
        });
    }

    /**
     * Test 2: Search filters by category dropdown
     * Tests: Category filter, combined search + filter, dynamic filtering
     */
    public function test_search_filters_by_category(): void
    {
        // Create multiple categories
        $category1 = ProductCategory::factory()->create(['name' => 'Category Alpha']);
        $category2 = ProductCategory::factory()->create(['name' => 'Category Beta']);

        // Create products in different categories
        $product1 = Product::factory()->create([
            'name' => 'Alpha Category Product',
            'sku' => 'ACP-001',
            'status' => 'active',
        ]);
        $product1->categories()->attach($category1->id, ['is_primary' => true, 'display_order' => 1]);

        $product2 = Product::factory()->create([
            'name' => 'Beta Category Product',
            'sku' => 'BCP-001',
            'status' => 'active',
        ]);
        $product2->categories()->attach($category2->id, ['is_primary' => true, 'display_order' => 1]);

        $product3 = Product::factory()->create([
            'name' => 'Another Alpha Product',
            'sku' => 'AAP-001',
            'status' => 'active',
        ]);
        $product3->categories()->attach($category1->id, ['is_primary' => true, 'display_order' => 1]);

        $this->browse(function (Browser $browser) use ($category1, $category2) {
            $browser->visit('/products')

                    // Verify all products initially visible
                    ->assertSee('Alpha Category Product')
                    ->assertSee('Beta Category Product')
                    ->assertSee('Another Alpha Product')

                    // Locate category filter dropdown (sidebar or top filter)
                    ->assertPresent('select[name="category"], a[data-category]')

                    // Select Category Alpha
                    // Note: May be a select dropdown OR clickable links
                    ->select('select[name="category"]', $category1->id)
                    ->pause(1000) // Wait for filter

                    // Verify filtered to Category Alpha only
                    ->assertSee('Alpha Category Product')
                    ->assertSee('Another Alpha Product')
                    ->assertDontSee('Beta Category Product')

                    // Now combine with search
                    ->type('input[name="search"], input[type="search"]', 'Another')
                    ->keys('input[name="search"], input[type="search"]', '{enter}')
                    ->pause(1000)

                    // Verify combined filter: Category Alpha + "Another" in name
                    ->assertSee('Another Alpha Product')
                    ->assertDontSee('Alpha Category Product') // Doesn't match "Another"
                    ->assertDontSee('Beta Category Product'); // Different category
        });
    }
}
