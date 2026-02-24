<?php

namespace Tests\Feature\Admin;

use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VariantManagementTest extends TestCase
{
    use RefreshDatabase;

    protected Customer $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Customer::factory()->create([
            'role' => 'admin',
            'is_admin' => true,
        ]);
    }

    /**
     * Test 1: Admin can update a single variant's retail_price and is_active
     */
    public function test_admin_can_update_variant_price_and_active_status(): void
    {
        $this->actingAs($this->admin);

        $product = Product::factory()->create(['price' => 30.00]);
        $variant = ProductVariant::factory()->forProduct($product)->create([
            'printful_cost' => 12.00,
            'retail_price' => 30.00,
            'is_active' => true,
        ]);

        $response = $this->patchJson(
            route('admin.products.variants.update', [$product, $variant]),
            [
                'retail_price' => 45.99,
                'is_active' => false,
            ]
        );

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'variant' => [
                'id' => $variant->id,
                'retail_price' => '45.99',
                'is_active' => false,
            ],
        ]);

        $this->assertDatabaseHas('product_variants', [
            'id' => $variant->id,
            'retail_price' => 45.99,
            'is_active' => false,
        ]);
    }

    /**
     * Test 2: Non-admin cannot access variant endpoints (403)
     */
    public function test_non_admin_cannot_access_variant_endpoints(): void
    {
        $customer = Customer::factory()->create([
            'role' => 'customer',
            'is_admin' => false,
        ]);

        $this->actingAs($customer);

        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->forProduct($product)->create();

        // Single update
        $response = $this->patchJson(
            route('admin.products.variants.update', [$product, $variant]),
            [
                'retail_price' => 25.00,
                'is_active' => true,
            ]
        );

        $response->assertForbidden();

        // Bulk update
        $response = $this->postJson(
            route('admin.products.variants.bulk-update', $product),
            [
                'variant_ids' => [$variant->id],
                'action' => 'activate',
                'value' => null,
            ]
        );

        $response->assertForbidden();
    }

    /**
     * Test 3: Bulk update with markup_percent applies correct price
     */
    public function test_bulk_update_markup_percent_applies_correct_price(): void
    {
        $this->actingAs($this->admin);

        $product = Product::factory()->create(['price' => 20.00]);
        $variant1 = ProductVariant::factory()->forProduct($product)->create([
            'printful_cost' => 10.00,
            'retail_price' => 20.00,
            'is_active' => true,
        ]);
        $variant2 = ProductVariant::factory()->forProduct($product)->create([
            'printful_cost' => 15.00,
            'retail_price' => 30.00,
            'is_active' => true,
        ]);

        // Apply 50% markup over cost
        $response = $this->postJson(
            route('admin.products.variants.bulk-update', $product),
            [
                'variant_ids' => [$variant1->id, $variant2->id],
                'action' => 'markup_percent',
                'value' => 50,
            ]
        );

        $response->assertOk();
        $response->assertJson(['success' => true]);

        // variant1: cost 10.00 * 1.50 = 15.00
        $this->assertDatabaseHas('product_variants', [
            'id' => $variant1->id,
            'retail_price' => 15.00,
        ]);

        // variant2: cost 15.00 * 1.50 = 22.50
        $this->assertDatabaseHas('product_variants', [
            'id' => $variant2->id,
            'retail_price' => 22.50,
        ]);
    }

    /**
     * Test 4: Bulk update with flat_price sets correct price
     */
    public function test_bulk_update_flat_price_sets_correct_price(): void
    {
        $this->actingAs($this->admin);

        $product = Product::factory()->create(['price' => 25.00]);
        $variant1 = ProductVariant::factory()->forProduct($product)->create([
            'printful_cost' => 10.00,
            'retail_price' => 25.00,
            'is_active' => true,
        ]);
        $variant2 = ProductVariant::factory()->forProduct($product)->create([
            'printful_cost' => 12.00,
            'retail_price' => 30.00,
            'is_active' => true,
        ]);

        $response = $this->postJson(
            route('admin.products.variants.bulk-update', $product),
            [
                'variant_ids' => [$variant1->id, $variant2->id],
                'action' => 'flat_price',
                'value' => 35.00,
            ]
        );

        $response->assertOk();
        $response->assertJson(['success' => true]);

        // Both variants should have the flat price
        $this->assertDatabaseHas('product_variants', [
            'id' => $variant1->id,
            'retail_price' => 35.00,
        ]);
        $this->assertDatabaseHas('product_variants', [
            'id' => $variant2->id,
            'retail_price' => 35.00,
        ]);
    }

    /**
     * Test 5: Bulk activate/deactivate toggles is_active
     */
    public function test_bulk_activate_and_deactivate_toggles_is_active(): void
    {
        $this->actingAs($this->admin);

        $product = Product::factory()->create(['price' => 20.00]);
        $variant1 = ProductVariant::factory()->forProduct($product)->create([
            'printful_cost' => 10.00,
            'retail_price' => 20.00,
            'is_active' => true,
        ]);
        $variant2 = ProductVariant::factory()->forProduct($product)->create([
            'printful_cost' => 12.00,
            'retail_price' => 24.00,
            'is_active' => true,
        ]);

        // Deactivate both
        $response = $this->postJson(
            route('admin.products.variants.bulk-update', $product),
            [
                'variant_ids' => [$variant1->id, $variant2->id],
                'action' => 'deactivate',
                'value' => null,
            ]
        );

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('product_variants', [
            'id' => $variant1->id,
            'is_active' => false,
        ]);
        $this->assertDatabaseHas('product_variants', [
            'id' => $variant2->id,
            'is_active' => false,
        ]);

        // Activate both
        $response = $this->postJson(
            route('admin.products.variants.bulk-update', $product),
            [
                'variant_ids' => [$variant1->id, $variant2->id],
                'action' => 'activate',
                'value' => null,
            ]
        );

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('product_variants', [
            'id' => $variant1->id,
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('product_variants', [
            'id' => $variant2->id,
            'is_active' => true,
        ]);
    }

    /**
     * Test 6: Variant must belong to the product (404)
     */
    public function test_variant_must_belong_to_the_product(): void
    {
        $this->actingAs($this->admin);

        $product = Product::factory()->create();
        $otherProduct = Product::factory()->create();
        $variant = ProductVariant::factory()->forProduct($otherProduct)->create();

        $response = $this->patchJson(
            route('admin.products.variants.update', [$product, $variant]),
            [
                'retail_price' => 25.00,
                'is_active' => true,
            ]
        );

        $response->assertNotFound();
    }

    /**
     * Test 7: Product base price syncs to min active variant after update
     */
    public function test_product_price_syncs_to_min_active_variant_after_update(): void
    {
        $this->actingAs($this->admin);

        $product = Product::factory()->create(['price' => 50.00]);
        $variantCheap = ProductVariant::factory()->forProduct($product)->create([
            'printful_cost' => 8.00,
            'retail_price' => 20.00,
            'is_active' => true,
        ]);
        $variantExpensive = ProductVariant::factory()->forProduct($product)->create([
            'printful_cost' => 15.00,
            'retail_price' => 40.00,
            'is_active' => true,
        ]);

        // Update the cheaper variant's price to be higher than the expensive one
        $response = $this->patchJson(
            route('admin.products.variants.update', [$product, $variantCheap]),
            [
                'retail_price' => 55.00,
                'is_active' => true,
            ]
        );

        $response->assertOk();

        // Product price should now be the min of active variants: 40.00
        $response->assertJsonPath('product_price', '40.00');
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'price' => 40.00,
        ]);
    }
}
