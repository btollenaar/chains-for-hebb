<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTrackingTest extends TestCase
{
    use RefreshDatabase;

    protected Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = Customer::factory()->create(['role' => 'customer']);
    }

    /**
     * Test 1: Authenticated user can view their order tracking page
     */
    public function test_authenticated_user_can_view_their_order_tracking_page(): void
    {
        // Arrange
        $order = Order::factory()->for($this->customer)->paid()->create([
            'fulfillment_status' => 'shipped',
            'tracking_number' => '9400111899223456789012',
            'tracking_carrier' => 'USPS',
            'shipped_at' => now()->subDays(2),
        ]);

        // Act
        $response = $this->actingAs($this->customer)
            ->get(route('orders.tracking', $order));

        // Assert
        $response->assertStatus(200);
        $response->assertSee($order->order_number);
    }

    /**
     * Test 2: Authenticated user cannot view another customer's tracking page (403)
     */
    public function test_authenticated_user_cannot_view_another_customers_tracking_page(): void
    {
        // Arrange
        $otherCustomer = Customer::factory()->create(['role' => 'customer']);
        $otherOrder = Order::factory()->for($otherCustomer)->paid()->create();

        // Act
        $response = $this->actingAs($this->customer)
            ->get(route('orders.tracking', $otherOrder));

        // Assert
        $response->assertStatus(403);
    }

    /**
     * Test 3: Public track form loads (GET /track)
     */
    public function test_public_track_form_loads(): void
    {
        // Act
        $response = $this->get(route('track.form'));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('Track Your Order');
        $response->assertSee('order_number');
        $response->assertSee('email');
    }

    /**
     * Test 4: Public track lookup finds order by order_number + email
     */
    public function test_public_track_lookup_finds_order_by_order_number_and_email(): void
    {
        // Arrange
        $order = Order::factory()->for($this->customer)->paid()->create([
            'fulfillment_status' => 'shipped',
            'tracking_number' => '1Z999AA10123456784',
            'tracking_carrier' => 'UPS',
            'shipped_at' => now()->subDay(),
        ]);

        // Act
        $response = $this->post(route('track.lookup'), [
            'order_number' => $order->order_number,
            'email' => $this->customer->email,
        ]);

        // Assert: Should show the tracking page with order details
        $response->assertStatus(200);
        $response->assertSee($order->order_number);
        $response->assertSee($order->tracking_number);
    }

    /**
     * Test 5: Public track lookup returns error for wrong email
     */
    public function test_public_track_lookup_returns_error_for_wrong_email(): void
    {
        // Arrange
        $order = Order::factory()->for($this->customer)->paid()->create();

        // Act
        $response = $this->post(route('track.lookup'), [
            'order_number' => $order->order_number,
            'email' => 'wrong@example.com',
        ]);

        // Assert: Should redirect back with errors
        $response->assertRedirect();
        $response->assertSessionHasErrors('order_number');
    }

    /**
     * Test 6: Public track lookup returns error for non-existent order
     */
    public function test_public_track_lookup_returns_error_for_nonexistent_order(): void
    {
        // Act
        $response = $this->post(route('track.lookup'), [
            'order_number' => 'ORD-DOESNOTEXIST',
            'email' => 'nobody@example.com',
        ]);

        // Assert: Should redirect back with errors
        $response->assertRedirect();
        $response->assertSessionHasErrors('order_number');
    }

    /**
     * Test 7: Tracking page shows tracking number when available
     */
    public function test_tracking_page_shows_tracking_number_when_available(): void
    {
        // Arrange
        $trackingNumber = '9261290100130412345678';
        $order = Order::factory()->for($this->customer)->paid()->create([
            'fulfillment_status' => 'shipped',
            'tracking_number' => $trackingNumber,
            'tracking_carrier' => 'USPS',
            'shipped_at' => now()->subDays(3),
        ]);

        // Act
        $response = $this->actingAs($this->customer)
            ->get(route('orders.tracking', $order));

        // Assert: Tracking number is visible on the page
        $response->assertStatus(200);
        $response->assertSee($trackingNumber);
        $response->assertSee('USPS');
        $response->assertSee('Tracking Information');
    }
}
