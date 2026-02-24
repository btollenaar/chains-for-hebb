<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Cart;
use App\Services\CouponService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponSystemTest extends TestCase
{
    use RefreshDatabase;

    protected Customer $admin;
    protected Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Customer::factory()->create([
            'role' => 'admin',
            'is_admin' => true,
        ]);

        $this->customer = Customer::factory()->create();
    }

    // --- Admin CRUD Tests ---

    public function test_admin_can_view_coupons_list(): void
    {
        Coupon::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.coupons.index'));

        $response->assertOk();
        $response->assertViewIs('admin.coupons.index');
    }

    public function test_admin_can_create_coupon(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.coupons.store'), [
            'code' => 'NEWCOUPON',
            'type' => 'percentage',
            'value' => 15,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.coupons.index'));
        $this->assertDatabaseHas('coupons', [
            'code' => 'NEWCOUPON',
            'type' => 'percentage',
            'value' => 15,
        ]);
    }

    public function test_admin_can_update_coupon(): void
    {
        $coupon = Coupon::factory()->create(['code' => 'OLD']);

        $response = $this->actingAs($this->admin)->put(route('admin.coupons.update', $coupon), [
            'code' => 'UPDATED',
            'type' => 'fixed',
            'value' => 25,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.coupons.index'));
        $this->assertDatabaseHas('coupons', [
            'id' => $coupon->id,
            'code' => 'UPDATED',
            'type' => 'fixed',
        ]);
    }

    public function test_admin_can_delete_coupon(): void
    {
        $coupon = Coupon::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('admin.coupons.destroy', $coupon));

        $response->assertRedirect(route('admin.coupons.index'));
        $this->assertSoftDeleted('coupons', ['id' => $coupon->id]);
    }

    public function test_non_admin_cannot_access_coupons(): void
    {
        $response = $this->actingAs($this->customer)->get(route('admin.coupons.index'));

        $response->assertForbidden();
    }

    public function test_coupon_code_is_auto_uppercased(): void
    {
        $this->actingAs($this->admin)->post(route('admin.coupons.store'), [
            'code' => 'lowercase',
            'type' => 'percentage',
            'value' => 10,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('coupons', ['code' => 'LOWERCASE']);
    }

    // --- Validation Tests ---

    public function test_valid_coupon_passes_validation(): void
    {
        $coupon = Coupon::factory()->percentage(10)->create();

        $service = new CouponService();
        $result = $service->validateCoupon($coupon->code, 100.00);

        $this->assertTrue($result['valid']);
        $this->assertEquals(10.00, $result['discount']);
    }

    public function test_expired_coupon_fails_validation(): void
    {
        $coupon = Coupon::factory()->expired()->create();

        $service = new CouponService();
        $result = $service->validateCoupon($coupon->code, 100.00);

        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('expired', $result['error']);
    }

    public function test_maxed_out_coupon_fails_validation(): void
    {
        $coupon = Coupon::factory()->maxedOut()->create();

        $service = new CouponService();
        $result = $service->validateCoupon($coupon->code, 100.00);

        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('usage limit', $result['error']);
    }

    public function test_inactive_coupon_fails_validation(): void
    {
        $coupon = Coupon::factory()->inactive()->create();

        $service = new CouponService();
        $result = $service->validateCoupon($coupon->code, 100.00);

        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('no longer active', $result['error']);
    }

    public function test_min_order_not_met_fails_validation(): void
    {
        $coupon = Coupon::factory()->percentage(10)->withMinOrder(100)->create();

        $service = new CouponService();
        $result = $service->validateCoupon($coupon->code, 50.00);

        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('Minimum order', $result['error']);
    }

    public function test_percentage_discount_capped_by_max_discount(): void
    {
        $coupon = Coupon::factory()->percentage(50)->withMaxDiscount(25)->create();

        $service = new CouponService();
        $result = $service->validateCoupon($coupon->code, 200.00);

        $this->assertTrue($result['valid']);
        // 50% of $200 = $100, but capped at $25
        $this->assertEquals(25.00, $result['discount']);
    }

    public function test_per_customer_limit_enforced(): void
    {
        $coupon = Coupon::factory()->percentage(10)->withMaxUsesPerCustomer(1)->create();

        // Simulate prior usage
        CouponUsage::create([
            'coupon_id' => $coupon->id,
            'customer_id' => $this->customer->id,
            'order_id' => Order::factory()->create(['customer_id' => $this->customer->id])->id,
            'discount_amount' => 10.00,
            'used_at' => now(),
        ]);

        $service = new CouponService();
        $result = $service->validateCoupon($coupon->code, 100.00, $this->customer->id);

        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('maximum number of times', $result['error']);
    }

    // --- Checkout Integration Tests ---

    public function test_checkout_with_valid_coupon_applies_discount(): void
    {
        $product = Product::factory()->create(['price' => 100.00, 'stock_quantity' => 10]);
        $coupon = Coupon::factory()->percentage(10)->create(['code' => 'TEST10']);

        Cart::create([
            'customer_id' => $this->customer->id,
            'item_type' => Product::class,
            'item_id' => $product->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->customer)->post(route('checkout.process'), [
            'name' => $this->customer->name,
            'email' => $this->customer->email,
            'shipping_street' => '123 Main St',
            'shipping_city' => 'Anytown',
            'shipping_state' => 'CA',
            'shipping_zip' => '90210',
            'shipping_country' => 'US',
            'same_as_shipping' => true,
            'payment_method' => 'cash',
            'shipping_method' => 'standard',
            'coupon_code' => 'TEST10',
        ]);

        $order = Order::where('customer_id', $this->customer->id)->latest()->first();

        $this->assertNotNull($order);
        $this->assertEquals('TEST10', $order->coupon_code);
        $this->assertGreaterThan(0, (float) $order->discount_amount);
    }

    public function test_checkout_with_invalid_coupon_returns_error(): void
    {
        $product = Product::factory()->create(['price' => 100.00, 'stock_quantity' => 10]);

        Cart::create([
            'customer_id' => $this->customer->id,
            'item_type' => Product::class,
            'item_id' => $product->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->customer)->post(route('checkout.process'), [
            'name' => $this->customer->name,
            'email' => $this->customer->email,
            'shipping_street' => '123 Main St',
            'shipping_city' => 'Anytown',
            'shipping_state' => 'CA',
            'shipping_zip' => '90210',
            'shipping_country' => 'US',
            'same_as_shipping' => true,
            'payment_method' => 'cash',
            'shipping_method' => 'standard',
            'coupon_code' => 'DOESNOTEXIST',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_coupon_usage_recorded_after_checkout(): void
    {
        $product = Product::factory()->create(['price' => 100.00, 'stock_quantity' => 10]);
        $coupon = Coupon::factory()->percentage(10)->create(['code' => 'TRACK10']);

        Cart::create([
            'customer_id' => $this->customer->id,
            'item_type' => Product::class,
            'item_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->actingAs($this->customer)->post(route('checkout.process'), [
            'name' => $this->customer->name,
            'email' => $this->customer->email,
            'shipping_street' => '123 Main St',
            'shipping_city' => 'Anytown',
            'shipping_state' => 'CA',
            'shipping_zip' => '90210',
            'shipping_country' => 'US',
            'same_as_shipping' => true,
            'payment_method' => 'cash',
            'shipping_method' => 'standard',
            'coupon_code' => 'TRACK10',
        ]);

        $this->assertDatabaseHas('coupon_usage', [
            'coupon_id' => $coupon->id,
            'customer_id' => $this->customer->id,
        ]);

        $coupon->refresh();
        $this->assertEquals(1, $coupon->used_count);
    }

    public function test_coupon_used_count_incremented(): void
    {
        $coupon = Coupon::factory()->percentage(10)->create(['used_count' => 5]);

        $order = Order::factory()->create(['customer_id' => $this->customer->id]);

        $coupon->recordUsage($this->customer->id, $order->id, 10.00);

        $coupon->refresh();
        $this->assertEquals(6, $coupon->used_count);
    }
}
