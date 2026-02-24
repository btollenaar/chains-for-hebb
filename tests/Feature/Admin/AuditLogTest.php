<?php

namespace Tests\Feature\Admin;

use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
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
     * Test 1: Admin can view the audit log index page with entries.
     */
    public function test_admin_can_view_audit_log_index(): void
    {
        // Arrange: Create some audit log entries
        AuditLog::create([
            'user_id' => $this->admin->id,
            'action' => 'created',
            'model_type' => 'Product',
            'model_id' => 1,
            'model_label' => 'Test Product',
            'new_values' => ['name' => 'Test Product', 'price' => 29.99],
            'ip_address' => '127.0.0.1',
        ]);

        AuditLog::create([
            'user_id' => $this->admin->id,
            'action' => 'updated',
            'model_type' => 'Order',
            'model_id' => 5,
            'model_label' => 'ORD-ABC123',
            'old_values' => ['status' => 'pending'],
            'new_values' => ['status' => 'paid'],
            'ip_address' => '127.0.0.1',
        ]);

        // Act
        $response = $this->actingAs($this->admin)
            ->get(route('admin.audit-logs.index'));

        // Assert
        $response->assertOk();
        $response->assertSee('Audit Log');
        $response->assertSee('Test Product');
        $response->assertSee('ORD-ABC123');
        $response->assertSee('Created');
        $response->assertSee('Updated');
    }

    /**
     * Test 2: Admin can view a single audit log detail page.
     */
    public function test_admin_can_view_audit_log_detail(): void
    {
        // Arrange
        $auditLog = AuditLog::create([
            'user_id' => $this->admin->id,
            'action' => 'updated',
            'model_type' => 'Product',
            'model_id' => 42,
            'model_label' => 'Classic Logo T-Shirt',
            'old_values' => ['price' => '12.99', 'stock_quantity' => 50],
            'new_values' => ['price' => '14.99', 'stock_quantity' => 75],
            'ip_address' => '192.168.1.100',
            'user_agent' => 'Mozilla/5.0 Test Browser',
        ]);

        // Act
        $response = $this->actingAs($this->admin)
            ->get(route('admin.audit-logs.show', $auditLog));

        // Assert
        $response->assertOk();
        $response->assertSee('Audit Log Detail');
        $response->assertSee('Classic Logo T-Shirt');
        $response->assertSee('12.99');
        $response->assertSee('14.99');
        $response->assertSee('192.168.1.100');
        $response->assertSee('Mozilla/5.0 Test Browser');
        $response->assertSee($this->admin->name);
    }

    /**
     * Test 3: The Auditable trait records a log entry when a model is created.
     */
    public function test_audit_log_records_model_creation(): void
    {
        // Arrange: Act as admin so the trait captures user_id
        $this->actingAs($this->admin);

        // Act: Create a product (which uses the Auditable trait)
        $product = Product::create([
            'name' => 'Classic Logo Mug',
            'slug' => 'classic-logo-mug',
            'description' => 'A ceramic mug with custom logo print.',
            'sku' => 'MUG-LOGO-001',
            'price' => 9.99,
            'stock_quantity' => 100,
            'status' => 'active',
        ]);

        // Assert: An audit log entry was created
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->admin->id,
            'action' => 'created',
            'model_type' => 'Product',
            'model_id' => $product->id,
            'model_label' => 'Classic Logo Mug',
        ]);

        $log = AuditLog::where('model_type', 'Product')
            ->where('model_id', $product->id)
            ->where('action', 'created')
            ->first();

        $this->assertNotNull($log);
        $this->assertNotNull($log->new_values);
        $this->assertEquals('Classic Logo Mug', $log->new_values['name']);
    }

    /**
     * Test 4: The Auditable trait records old and new values when a model is updated.
     */
    public function test_audit_log_records_model_update(): void
    {
        // Arrange
        $this->actingAs($this->admin);

        $product = Product::create([
            'name' => 'Compost Bin',
            'slug' => 'compost-bin',
            'description' => 'Kitchen compost bin.',
            'sku' => 'ECO-COMP-001',
            'price' => 34.99,
            'stock_quantity' => 50,
            'status' => 'active',
        ]);

        // Clear the 'created' log so we can isolate the 'updated' log
        AuditLog::where('action', 'created')->delete();

        // Act: Update the product
        $product->update([
            'price' => 39.99,
            'stock_quantity' => 75,
        ]);

        // Assert: An audit log entry was created with old and new values
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->admin->id,
            'action' => 'updated',
            'model_type' => 'Product',
            'model_id' => $product->id,
        ]);

        $log = AuditLog::where('model_type', 'Product')
            ->where('model_id', $product->id)
            ->where('action', 'updated')
            ->first();

        $this->assertNotNull($log);
        $this->assertNotNull($log->old_values);
        $this->assertNotNull($log->new_values);

        // Old values should contain the previous price and stock
        $this->assertEquals('34.99', $log->old_values['price']);
        $this->assertEquals(50, $log->old_values['stock_quantity']);

        // New values should contain the updated price and stock
        $this->assertEquals('39.99', $log->new_values['price']);
        $this->assertEquals(75, $log->new_values['stock_quantity']);
    }

    /**
     * Test 5: Non-admin customers cannot access the audit log.
     */
    public function test_non_admin_cannot_access_audit_log(): void
    {
        // Arrange
        $customer = Customer::factory()->create([
            'role' => 'customer',
            'is_admin' => false,
        ]);

        // Act
        $response = $this->actingAs($customer)
            ->get(route('admin.audit-logs.index'));

        // Assert: Should be forbidden or redirected
        $this->assertTrue(
            in_array($response->status(), [403, 302]),
            "Expected 403 or 302, got {$response->status()}"
        );
    }
}
