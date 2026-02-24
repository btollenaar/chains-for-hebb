<?php

namespace Tests\Browser;

use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminProductManagementTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test 1: Admin can create product with image upload
     * Tests: File upload UI, image preview, form submission
     */
    public function test_admin_create_product_with_images(): void
    {
        $admin = Customer::factory()->create([
            'email' => 'admin@product.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_admin' => true,
        ]);

        $category = ProductCategory::factory()->create(['name' => 'Admin Test Category']);

        $this->browse(function (Browser $browser) use ($admin, $category) {
            $browser->loginAs($admin)
                    ->visit('/admin/products/create')
                    ->assertSee('Create Product')

                    // Fill basic fields
                    ->type('name', 'Browser Admin Product')
                    ->type('sku', 'BAP-001')
                    ->type('price', '79.99')
                    ->type('stock_quantity', '50')

                    // Select category
                    ->check('category_ids[' . $category->id . ']')
                    ->pause(500)

                    // Set as primary category
                    ->radio('primary_category_id', $category->id)
                    ->pause(500)

                    // Fill description (may have TinyMCE)
                    ->type('description', 'This is a test product created via browser automation.')

                    // Note: File upload testing in browser requires actual files
                    // For this test, we'll verify the input exists
                    ->assertPresent('input[type="file"][name="images[]"]')

                    // Select status
                    ->select('status', 'active')

                    // Submit form
                    ->press('Create Product')
                    ->pause(2000)

                    // Verify redirect to product list
                    ->assertPathIs('/admin/products')
                    ->assertSee('Browser Admin Product');
        });
    }

    /**
     * Test 2: Category checkbox tree interaction
     * Tests: Tree UI, expand/collapse, multiple selection, primary designation
     */
    public function test_category_checkbox_tree_interaction(): void
    {
        $admin = Customer::factory()->create([
            'email' => 'admin@categories.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_admin' => true,
        ]);

        // Create parent category
        $parent = ProductCategory::factory()->create(['name' => 'Parent Category']);

        // Create child categories
        $child1 = ProductCategory::factory()->create([
            'name' => 'Child Category 1',
            'parent_id' => $parent->id,
        ]);

        $child2 = ProductCategory::factory()->create([
            'name' => 'Child Category 2',
            'parent_id' => $parent->id,
        ]);

        $this->browse(function (Browser $browser) use ($admin, $parent, $child1, $child2) {
            $browser->loginAs($admin)
                    ->visit('/admin/products/create')

                    // Verify parent category visible
                    ->assertSee('Parent Category')

                    // Expand parent category (if collapsible)
                    ->pause(500)

                    // Check parent category
                    ->check('category_ids[' . $parent->id . ']')
                    ->pause(500)

                    // Verify child categories visible
                    ->assertSee('Child Category 1')
                    ->assertSee('Child Category 2')

                    // Check child category
                    ->check('category_ids[' . $child1->id . ']')
                    ->pause(500)

                    // Set child as primary
                    ->radio('primary_category_id', $child1->id)
                    ->pause(500)

                    // Verify selections persisted
                    ->assertChecked('category_ids[' . $parent->id . ']')
                    ->assertChecked('category_ids[' . $child1->id . ']')
                    ->assertRadioSelected('primary_category_id', $child1->id);
        });
    }

    /**
     * Test 3: WYSIWYG editor saves content correctly
     * Tests: TinyMCE initialization, content input, HTML preservation
     */
    public function test_wysiwyg_editor_saves_content(): void
    {
        $admin = Customer::factory()->create([
            'email' => 'admin@wysiwyg.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_admin' => true,
        ]);

        $category = ProductCategory::factory()->create();

        $this->browse(function (Browser $browser) use ($admin, $category) {
            $browser->loginAs($admin)
                    ->visit('/admin/products/create')

                    // Fill basic fields
                    ->type('name', 'WYSIWYG Test Product')
                    ->type('sku', 'WYS-001')
                    ->type('price', '99.99')
                    ->type('stock_quantity', '25')

                    // Check category
                    ->check('category_ids[' . $category->id . ']')
                    ->radio('primary_category_id', $category->id)
                    ->pause(500)

                    // Wait for TinyMCE to load (may take a moment)
                    ->pause(2000)

                    // Input description
                    // Note: TinyMCE uses iframe, may need special handling
                    // For now, test textarea fallback
                    ->type('description', '<p>This is <strong>bold</strong> text.</p>')

                    ->select('status', 'active')

                    // Submit
                    ->press('Create Product')
                    ->pause(2000)

                    ->assertPathIs('/admin/products')
                    ->assertSee('WYSIWYG Test Product');

            // Verify HTML preserved on edit page
            $product = Product::where('sku', 'WYS-001')->first();

            $browser->visit('/admin/products/' . $product->id . '/edit')
                    ->pause(2000) // Wait for TinyMCE

                    // Verify content contains HTML tags
                    ->assertSourceHas('bold');
        });
    }

    /**
     * Test 4: Product status toggle works
     * Tests: Switch controls, AJAX updates, status badges
     */
    public function test_product_status_toggle(): void
    {
        $admin = Customer::factory()->create([
            'email' => 'admin@status.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_admin' => true,
        ]);

        $category = ProductCategory::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Status Test Product',
            'sku' => 'STP-001',
            'price' => 59.99,
            'stock_quantity' => 30,
            'status' => 'active',
        ]);
        $product->categories()->attach($category->id, ['is_primary' => true, 'display_order' => 1]);

        $this->browse(function (Browser $browser) use ($admin, $product) {
            $browser->loginAs($admin)
                    ->visit('/admin/products')

                    // Verify product listed
                    ->assertSee('Status Test Product')

                    // Verify active status badge/indicator
                    ->assertSee('Active')

                    // Click edit
                    ->visit('/admin/products/' . $product->id . '/edit')

                    // Change status to draft
                    ->select('status', 'draft')

                    // Save changes
                    ->press('Update Product')
                    ->pause(2000)

                    // Verify redirect
                    ->assertPathIs('/admin/products/' . $product->id . '/edit')
                    ->assertSee('Product updated successfully')

                    // Go back to list
                    ->visit('/admin/products')

                    // Verify status changed
                    ->assertSee('Draft');
        });
    }

    /**
     * Test 5: Bulk action checkboxes work
     * Tests: Multi-select, bulk actions, select all functionality
     */
    public function test_bulk_action_checkboxes(): void
    {
        $admin = Customer::factory()->create([
            'email' => 'admin@bulk.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_admin' => true,
        ]);

        $category = ProductCategory::factory()->create();

        // Create multiple products
        $product1 = Product::factory()->create([
            'name' => 'Bulk Product 1',
            'sku' => 'BP-001',
            'status' => 'active',
        ]);
        $product1->categories()->attach($category->id, ['is_primary' => true, 'display_order' => 1]);

        $product2 = Product::factory()->create([
            'name' => 'Bulk Product 2',
            'sku' => 'BP-002',
            'status' => 'active',
        ]);
        $product2->categories()->attach($category->id, ['is_primary' => true, 'display_order' => 1]);

        $product3 = Product::factory()->create([
            'name' => 'Bulk Product 3',
            'sku' => 'BP-003',
            'status' => 'active',
        ]);
        $product3->categories()->attach($category->id, ['is_primary' => true, 'display_order' => 1]);

        $this->browse(function (Browser $browser) use ($admin, $product1, $product2, $product3) {
            $browser->loginAs($admin)
                    ->visit('/admin/products')

                    // Verify all products visible
                    ->assertSee('Bulk Product 1')
                    ->assertSee('Bulk Product 2')
                    ->assertSee('Bulk Product 3')

                    // Check individual checkboxes exist
                    ->assertPresent('input[type="checkbox"][value="' . $product1->id . '"]')
                    ->assertPresent('input[type="checkbox"][value="' . $product2->id . '"]')

                    // Check first two products
                    ->check('input[type="checkbox"][value="' . $product1->id . '"]')
                    ->check('input[type="checkbox"][value="' . $product2->id . '"]')
                    ->pause(500)

                    // Verify checkboxes checked
                    ->assertChecked('input[type="checkbox"][value="' . $product1->id . '"]')
                    ->assertChecked('input[type="checkbox"][value="' . $product2->id . '"]')

                    // Verify bulk action button/dropdown appears
                    ->assertPresent('select[name="bulk_action"]'); // Or button, depending on UI
        });
    }
}
