<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationPolicyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 1: Customers can only view their own orders
     * CRITICAL SECURITY - prevents unauthorized order access
     */
    public function test_customers_can_only_view_their_own_orders(): void
    {
        // Arrange
        $customer1 = Customer::factory()->create(['role' => 'customer']);
        $customer2 = Customer::factory()->create(['role' => 'customer']);

        $order1 = Order::factory()->for($customer1)->create();
        $order2 = Order::factory()->for($customer2)->create();

        // Act & Assert: Customer 1 can view their own order
        $this->actingAs($customer1);
        $this->assertTrue($customer1->can('view', $order1));

        // Assert: Customer 1 CANNOT view customer 2's order
        $this->assertFalse($customer1->can('view', $order2));

        // Act & Assert: Admin can view all orders
        $admin = Customer::factory()->create(['role' => 'admin', 'is_admin' => true]);
        $this->actingAs($admin);
        $this->assertTrue($admin->can('view', $order1));
        $this->assertTrue($admin->can('view', $order2));
    }

    /**
     * Test 2: Only admins can create products
     * Prevents unauthorized catalog management
     */
    public function test_only_admins_can_create_products(): void
    {
        // Arrange
        $customer = Customer::factory()->create(['role' => 'customer']);
        $provider = Customer::factory()->create(['role' => 'provider']);
        $frontDesk = Customer::factory()->create(['role' => 'front_desk']);
        $admin = Customer::factory()->create(['role' => 'admin', 'is_admin' => true]);

        // Assert: Regular customer CANNOT create products
        $this->actingAs($customer);
        $this->assertFalse($customer->can('create', Product::class));

        // Assert: Provider CANNOT create products
        $this->actingAs($provider);
        $this->assertFalse($provider->can('create', Product::class));

        // Assert: Front desk CANNOT create products (even though they're staff)
        $this->actingAs($frontDesk);
        $this->assertFalse($frontDesk->can('create', Product::class));

        // Assert: Admin CAN create products
        $this->actingAs($admin);
        $this->assertTrue($admin->can('create', Product::class));
    }

    /**
     * Test 3: Mass assignment protection prevents role escalation
     * CRITICAL SECURITY - prevents privilege escalation attacks
     */
    public function test_mass_assignment_protection_prevents_role_escalation(): void
    {
        // Arrange: Attempt to register as admin via mass assignment
        $response = $this->post(route('register'), [
            'name' => 'Hacker',
            'email' => 'hacker@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'admin', // Attempting role escalation
            'is_admin' => true, // Attempting admin flag
        ]);

        // Assert: User created but NOT as admin
        $user = Customer::where('email', 'hacker@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNotEquals('admin', $user->role);
        $this->assertFalse($user->is_admin);
        $this->assertEquals('customer', $user->role); // Default role
    }

    /**
     * Test 4: Staff roles have elevated permissions for orders
     * Tests that front_desk role has admin capabilities for orders
     */
    public function test_staff_have_elevated_permissions(): void
    {
        // Arrange
        $frontDesk = Customer::factory()->create(['role' => 'front_desk']);
        $customer = Customer::factory()->create(['role' => 'customer']);
        $order = Order::factory()->for($customer)->create();

        // Act & Assert: Front desk can view all orders
        $this->actingAs($frontDesk);
        $this->assertTrue($frontDesk->can('view', $order));

        // Assert: Front desk can update orders
        $this->assertTrue($frontDesk->can('update', $order));
    }

    /**
     * Test 5: Customers cannot access admin panel
     * Tests route protection for admin-only areas
     */
    public function test_customers_cannot_access_admin_panel(): void
    {
        // Arrange
        $customer = Customer::factory()->create(['role' => 'customer']);

        // Act: Attempt to access admin products page
        $this->actingAs($customer);
        $response = $this->get(route('admin.products.index'));

        // Assert: Redirected away (403 Forbidden)
        $response->assertStatus(403);
    }

    /**
     * Test 7: Policy enforcement in controllers
     * Verifies policies are actually called in controller methods
     */
    public function test_policy_enforcement_in_controllers(): void
    {
        // Arrange
        $customer = Customer::factory()->create(['role' => 'customer']);
        $product = Product::factory()->create();

        // Act: Attempt to update product as customer (should fail)
        $this->actingAs($customer);
        $response = $this->put(route('admin.products.update', $product), [
            'name' => 'Hacked Product',
            'price' => 1.00,
        ]);

        // Assert: Access denied (403)
        $response->assertStatus(403);

        // Assert: Product unchanged
        $product->refresh();
        $this->assertNotEquals('Hacked Product', $product->name);
    }

    /**
     * Test 7: Guests redirected to login for protected routes
     * Tests authentication middleware on protected resources
     */
    public function test_guests_redirected_to_login_for_protected_routes(): void
    {
        // Act: Try to access orders page as guest
        $response = $this->get(route('orders.index'));

        // Assert: Redirected to login
        $response->assertRedirect(route('login'));
    }

    /**
     * Test 9: Admin can delete any order
     * Tests admin super-privileges
     */
    public function test_admin_can_delete_any_order(): void
    {
        // Arrange
        $admin = Customer::factory()->create(['role' => 'admin', 'is_admin' => true]);
        $customer = Customer::factory()->create(['role' => 'customer']);
        $order = Order::factory()->for($customer)->create();

        // Act & Assert: Admin can delete customer's order
        $this->actingAs($admin);
        $this->assertTrue($admin->can('delete', $order));

        // Assert: Customer CANNOT delete their own order
        $this->actingAs($customer);
        $this->assertFalse($customer->can('delete', $order));
    }

}
