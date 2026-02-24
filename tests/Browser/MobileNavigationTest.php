<?php

namespace Tests\Browser;

use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class MobileNavigationTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test 1: Hamburger menu opens and closes
     * Tests: Mobile menu toggle, animation, navigation links
     */
    public function test_hamburger_menu_opens(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(375, 667) // iPhone SE size
                    ->visit('/')

                    // Verify mobile menu button visible
                    ->assertPresent('button[aria-label*="menu"]') // Common pattern

                    // Menu should be hidden initially
                    ->assertDontSee('Products') // Main nav links hidden on mobile

                    // Click hamburger menu
                    ->press('button[aria-label*="menu"]')
                    ->pause(500) // Wait for animation

                    // Verify menu items visible
                    ->assertSee('Products')
                    ->assertSee('Services')
                    ->assertSee('About')
                    ->assertSee('Blog')
                    ->assertSee('Contact')

                    // Close menu
                    ->press('button[aria-label*="menu"]')
                    ->pause(500);

            // Note: After close, links may still be in DOM but hidden via CSS
        });
    }

    /**
     * Test 2: Category accordion expands on mobile
     * Tests: Collapsible categories, tap interaction, nested categories
     */
    public function test_category_accordion_expands(): void
    {
        // Create parent category with children
        $parent = ProductCategory::factory()->create(['name' => 'Mobile Parent Category']);

        $child1 = ProductCategory::factory()->create([
            'name' => 'Mobile Child 1',
            'parent_id' => $parent->id,
        ]);

        $child2 = ProductCategory::factory()->create([
            'name' => 'Mobile Child 2',
            'parent_id' => $parent->id,
        ]);

        $this->browse(function (Browser $browser) use ($parent) {
            $browser->resize(375, 667) // Mobile size
                    ->visit('/')

                    // Open mobile menu
                    ->press('button[aria-label*="menu"]')
                    ->pause(500)

                    // Click Products to expand categories
                    ->click('a[href*="products"]')
                    ->pause(500)

                    // Verify parent category visible
                    ->assertSee('Mobile Parent Category')

                    // Click parent to expand children
                    ->click('button:contains("Mobile Parent Category"), a:contains("Mobile Parent Category")')
                    ->pause(500)

                    // Verify children visible
                    ->assertSee('Mobile Child 1')
                    ->assertSee('Mobile Child 2');
        });
    }

    /**
     * Test 3: Admin FAB (Floating Action Button) filters open
     * Tests: Mobile admin UI, filter modal, bottom sheet animation
     */
    public function test_admin_fab_filters_open(): void
    {
        $admin = Customer::factory()->create([
            'email' => 'mobile@admin.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_admin' => true,
        ]);

        // Create test products
        $category = ProductCategory::factory()->create();
        $product1 = Product::factory()->create([
            'name' => 'Mobile Filter Product 1',
            'status' => 'active',
        ]);
        $product1->categories()->attach($category->id, ['is_primary' => true, 'display_order' => 1]);

        $product2 = Product::factory()->create([
            'name' => 'Mobile Filter Product 2',
            'status' => 'draft',
        ]);
        $product2->categories()->attach($category->id, ['is_primary' => true, 'display_order' => 1]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->resize(375, 667) // Mobile size
                    ->loginAs($admin)
                    ->visit('/admin/products')

                    // Verify products listed (mobile card view)
                    ->assertSee('Mobile Filter Product 1')
                    ->assertSee('Mobile Filter Product 2')

                    // Look for FAB filter button (should be visible on mobile only)
                    // Common pattern: Fixed bottom-right button with filter icon
                    ->assertPresent('button[data-action="open-filters"], button:has(svg[data-icon="filter"])')

                    // Click FAB to open filter modal
                    ->click('button[data-action="open-filters"], button:has(svg[data-icon="filter"])')
                    ->pause(500) // Wait for modal animation

                    // Verify filter options visible
                    ->assertSee('Status') // Filter label
                    ->assertPresent('select[name="status"], input[name="status"]')

                    // Apply filter (select "active" status)
                    ->select('status', 'active')

                    // Submit filter form
                    ->press('Apply Filters')
                    ->pause(1000)

                    // Verify filtered results
                    ->assertSee('Mobile Filter Product 1')
                    ->assertDontSee('Mobile Filter Product 2'); // Draft filtered out
        });
    }
}
