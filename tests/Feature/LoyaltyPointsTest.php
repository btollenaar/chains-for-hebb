<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\LoyaltyPoint;
use App\Models\Order;
use App\Models\Setting;
use App\Services\LoyaltyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoyaltyPointsTest extends TestCase
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

        $this->customer = Customer::factory()->create([
            'loyalty_points_balance' => 500,
        ]);

        // Seed loyalty settings
        Setting::updateOrCreate(
            ['category' => 'loyalty', 'key' => 'points_per_dollar'],
            ['value' => '1', 'type' => 'number']
        );
        Setting::updateOrCreate(
            ['category' => 'loyalty', 'key' => 'points_per_dollar_discount'],
            ['value' => '100', 'type' => 'number']
        );
    }

    public function test_authenticated_user_can_view_loyalty_page(): void
    {
        $response = $this->actingAs($this->customer)->get(route('loyalty.index'));

        $response->assertOk();
        $response->assertViewIs('loyalty.index');
        $response->assertSee('500');
    }

    public function test_guest_cannot_view_loyalty_page(): void
    {
        $response = $this->get(route('loyalty.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_earn_points_via_service(): void
    {
        $loyaltyService = app(LoyaltyService::class);

        $loyaltyService->earnPoints(
            $this->customer,
            50,
            'order',
            1,
            'Earned 50 points from Order #ORD-TEST'
        );

        $this->customer->refresh();
        $this->assertEquals(550, $this->customer->loyalty_points_balance);

        $this->assertDatabaseHas('loyalty_points', [
            'customer_id' => $this->customer->id,
            'points' => 50,
            'type' => 'earned',
            'source' => 'order',
            'source_id' => 1,
            'balance_after' => 550,
        ]);
    }

    public function test_redeem_points_via_service(): void
    {
        $loyaltyService = app(LoyaltyService::class);

        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'subtotal' => 100.00,
            'total_amount' => 100.00,
        ]);

        $loyaltyService->redeemPoints($this->customer, 200, $order);

        $this->customer->refresh();
        $order->refresh();

        $this->assertEquals(300, $this->customer->loyalty_points_balance);
        $this->assertEquals(200, $order->loyalty_points_redeemed);
        $this->assertEquals(2.00, $order->loyalty_discount);

        $this->assertDatabaseHas('loyalty_points', [
            'customer_id' => $this->customer->id,
            'points' => -200,
            'type' => 'redeemed',
            'balance_after' => 300,
        ]);
    }

    public function test_cannot_redeem_more_points_than_balance(): void
    {
        $loyaltyService = app(LoyaltyService::class);

        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'subtotal' => 100.00,
            'total_amount' => 100.00,
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Insufficient loyalty points balance.');

        $loyaltyService->redeemPoints($this->customer, 1000, $order);
    }

    public function test_admin_can_adjust_customer_points(): void
    {
        $response = $this->actingAs($this->admin)->post(
            route('admin.customers.loyalty-adjust', $this->customer),
            [
                'points' => 100,
                'description' => 'Bonus points for loyal customer',
            ]
        );

        $response->assertRedirect();

        $this->customer->refresh();
        $this->assertEquals(600, $this->customer->loyalty_points_balance);

        $this->assertDatabaseHas('loyalty_points', [
            'customer_id' => $this->customer->id,
            'points' => 100,
            'type' => 'adjusted',
            'description' => 'Bonus points for loyal customer',
            'balance_after' => 600,
        ]);
    }

    public function test_admin_can_deduct_customer_points(): void
    {
        $response = $this->actingAs($this->admin)->post(
            route('admin.customers.loyalty-adjust', $this->customer),
            [
                'points' => -200,
                'description' => 'Manual deduction',
            ]
        );

        $response->assertRedirect();

        $this->customer->refresh();
        $this->assertEquals(300, $this->customer->loyalty_points_balance);

        $this->assertDatabaseHas('loyalty_points', [
            'customer_id' => $this->customer->id,
            'points' => -200,
            'type' => 'adjusted',
            'balance_after' => 300,
        ]);
    }

    public function test_loyalty_discount_appears_on_order(): void
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'subtotal' => 100.00,
            'tax_amount' => 6.50,
            'shipping_cost' => 5.99,
            'discount_amount' => 0,
            'loyalty_points_redeemed' => 300,
            'loyalty_discount' => 3.00,
            'total_amount' => 109.49,
        ]);

        $this->assertEquals(300, $order->loyalty_points_redeemed);
        $this->assertEquals(3.00, $order->loyalty_discount);
    }

    public function test_calculate_points_for_order(): void
    {
        $loyaltyService = app(LoyaltyService::class);

        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'subtotal' => 75.50,
            'total_amount' => 80.00,
        ]);

        $points = $loyaltyService->calculatePointsForOrder($order);

        // 1 point per $1 spent, floor(75.50) = 75
        $this->assertEquals(75, $points);
    }

    public function test_max_redeemable_points_caps_at_50_percent(): void
    {
        $loyaltyService = app(LoyaltyService::class);

        // Customer has 500 points, 50% of $20 subtotal = $10 max discount = 1000 points needed
        // But customer only has 500 points, so max is 500
        $maxPoints = $loyaltyService->getMaxRedeemablePoints($this->customer, 20.00);
        $this->assertEquals(500, $maxPoints);

        // Customer has 500 points, 50% of $6 subtotal = $3 max discount = 300 points max
        $maxPoints = $loyaltyService->getMaxRedeemablePoints($this->customer, 6.00);
        $this->assertEquals(300, $maxPoints);
    }

    public function test_transaction_history_shows_on_loyalty_page(): void
    {
        LoyaltyPoint::create([
            'customer_id' => $this->customer->id,
            'points' => 100,
            'type' => 'earned',
            'source' => 'order',
            'source_id' => 1,
            'description' => 'Earned 100 points from Order #ORD-TEST1',
            'balance_after' => 600,
        ]);

        LoyaltyPoint::create([
            'customer_id' => $this->customer->id,
            'points' => -50,
            'type' => 'redeemed',
            'source' => 'order',
            'source_id' => 2,
            'description' => 'Redeemed 50 points on Order #ORD-TEST2',
            'balance_after' => 550,
        ]);

        $response = $this->actingAs($this->customer)->get(route('loyalty.index'));

        $response->assertOk();
        $response->assertSee('Earned 100 points from Order #ORD-TEST1');
        $response->assertSee('Redeemed 50 points on Order #ORD-TEST2');
    }
}
