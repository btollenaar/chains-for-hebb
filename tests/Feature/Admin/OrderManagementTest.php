<?php

namespace Tests\Feature\Admin;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderManagementTest extends TestCase
{
    use RefreshDatabase;

    protected Customer $admin;
    protected Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = Customer::factory()->create([
            'role' => 'admin',
            'is_admin' => true,
        ]);

        // Create a regular customer
        $this->customer = Customer::factory()->create([
            'role' => 'customer',
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
        ]);
    }

    /**
     * Test 1: Admin can view order details
     * Tests order detail page access
     */
    public function test_admin_can_view_order_details(): void
    {
        // Arrange
        $this->actingAs($this->admin);

        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'order_number' => 'ORD-12345',
            'subtotal' => 100.00,
            'tax_amount' => 7.00,
            'total_amount' => 107.00,
            'payment_status' => 'paid',
            'fulfillment_status' => 'pending',
        ]);

        // Add an order item
        $product = Product::factory()->create(['name' => 'Test Product']);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'item_type' => Product::class,
            'item_id' => $product->id,
            'name' => 'Test Product',
            'quantity' => 2,
            'unit_price' => 50.00,
            'total' => 100.00,
        ]);

        // Act
        $response = $this->get(route('admin.orders.show', $order));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('ORD-12345');
        $response->assertSee('Test Customer');
        $response->assertSee('$107.00');
    }

    /**
     * Test 2: Admin can update payment status
     * Tests payment status management
     */
    public function test_admin_can_update_payment_status(): void
    {
        // Arrange
        $this->actingAs($this->admin);

        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'payment_status' => 'pending',
            'fulfillment_status' => 'pending',
        ]);

        // Act
        $response = $this->put(route('admin.orders.update', $order), [
            'payment_status' => 'paid',
            'fulfillment_status' => 'pending',
        ]);

        // Assert
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'payment_status' => 'paid',
        ]);

        $response->assertRedirect(route('admin.orders.show', $order));
        $response->assertSessionHas('success');
    }

    /**
     * Test 3: Admin can update fulfillment status
     * Tests order fulfillment tracking
     */
    public function test_admin_can_update_fulfillment_status(): void
    {
        // Arrange
        $this->actingAs($this->admin);

        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'payment_status' => 'paid',
            'fulfillment_status' => 'pending',
        ]);

        // Act: Update through statuses
        $this->put(route('admin.orders.update', $order), [
            'payment_status' => 'paid',
            'fulfillment_status' => 'processing',
        ]);

        $order->refresh();
        $this->assertEquals('processing', $order->fulfillment_status);

        // Act: Mark as completed
        $this->put(route('admin.orders.update', $order), [
            'payment_status' => 'paid',
            'fulfillment_status' => 'completed',
        ]);

        $order->refresh();
        $this->assertEquals('completed', $order->fulfillment_status);
    }

    /**
     * Test 4: Admin can add notes to orders
     * Tests admin notes functionality
     */
    public function test_admin_can_add_notes_to_orders(): void
    {
        // Arrange
        $this->actingAs($this->admin);

        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
        ]);

        // Act
        $this->put(route('admin.orders.update', $order), [
            'payment_status' => 'paid',
            'fulfillment_status' => 'pending',
            'admin_notes' => 'Customer requested expedited shipping',
        ]);

        // Assert
        $order->refresh();
        $this->assertEquals('Customer requested expedited shipping', $order->admin_notes);
    }

    /**
     * Test 5: Order list filtered by payment status
     * Tests payment status filtering
     */
    public function test_order_list_filtered_by_payment_status(): void
    {
        // Arrange
        $this->actingAs($this->admin);

        Order::factory()->create(['payment_status' => 'pending']);
        Order::factory()->create(['payment_status' => 'paid']);
        Order::factory()->create(['payment_status' => 'failed']);

        // Act: Filter by paid status
        $response = $this->get(route('admin.orders.index', ['payment_status' => 'paid']));

        // Assert
        $response->assertStatus(200);
        // View should contain filtered results
    }

    /**
     * Test 6: Order list filtered by fulfillment status
     * Tests fulfillment status filtering
     */
    public function test_order_list_filtered_by_fulfillment_status(): void
    {
        // Arrange
        $this->actingAs($this->admin);

        Order::factory()->create(['fulfillment_status' => 'pending']);
        Order::factory()->create(['fulfillment_status' => 'processing']);
        Order::factory()->create(['fulfillment_status' => 'completed']);

        // Act: Filter by completed status
        $response = $this->get(route('admin.orders.index', ['fulfillment_status' => 'completed']));

        // Assert
        $response->assertStatus(200);
    }

    /**
     * Test 7: Order search by order number
     * Tests search functionality
     */
    public function test_order_search_by_order_number(): void
    {
        // Arrange
        $this->actingAs($this->admin);

        $order1 = Order::factory()->create(['order_number' => 'ORD-ABC123']);
        $order2 = Order::factory()->create(['order_number' => 'ORD-XYZ789']);

        // Act: Search by order number
        $response = $this->get(route('admin.orders.index', ['search' => 'ABC123']));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('ORD-ABC123');
    }

    /**
     * Test 8: Order search by customer name and email
     * Tests customer search functionality
     */
    public function test_order_search_by_customer_name_and_email(): void
    {
        // Arrange
        $this->actingAs($this->admin);

        $customer1 = Customer::factory()->create([
            'name' => 'Alice Johnson',
            'email' => 'alice@example.com',
        ]);
        $customer2 = Customer::factory()->create([
            'name' => 'Bob Smith',
            'email' => 'bob@example.com',
        ]);

        Order::factory()->create(['customer_id' => $customer1->id]);
        Order::factory()->create(['customer_id' => $customer2->id]);

        // Act: Search by name
        $response = $this->get(route('admin.orders.index', ['search' => 'Alice']));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('Alice Johnson');

        // Act: Search by email
        $response = $this->get(route('admin.orders.index', ['search' => 'bob@example']));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('Bob Smith');
    }

    /**
     * Test 9: CSV export includes order data
     * Tests export functionality
     */
    public function test_csv_export_includes_order_data(): void
    {
        // Arrange
        $this->actingAs($this->admin);

        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'order_number' => 'ORD-TEST-001',
            'subtotal' => 100.00,
            'tax_amount' => 7.00,
            'total_amount' => 107.00,
            'payment_status' => 'paid',
            'fulfillment_status' => 'completed',
        ]);

        // Act
        $response = $this->get(route('admin.orders.export'));

        // Assert
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');

        // Parse CSV content
        $content = $response->streamedContent();
        $this->assertStringContainsString('ORD-TEST-001', $content);
        $this->assertStringContainsString('Test Customer', $content);
        $this->assertStringContainsString('customer@example.com', $content);
    }

    /**
     * Test 10: Invalid payment status rejected
     * Tests validation of status fields
     */
    public function test_invalid_payment_status_rejected(): void
    {
        // Arrange
        $this->actingAs($this->admin);

        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
        ]);

        // Act: Try to set invalid payment status
        $response = $this->put(route('admin.orders.update', $order), [
            'payment_status' => 'invalid_status',
            'fulfillment_status' => 'pending',
        ]);

        // Assert: Validation error
        $response->assertSessionHasErrors('payment_status');
    }
}
