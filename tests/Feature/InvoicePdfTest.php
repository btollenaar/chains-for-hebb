<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoicePdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_download_invoice_for_paid_order(): void
    {
        $customer = Customer::factory()->create();
        $order = Order::factory()->paid()->create(['customer_id' => $customer->id]);

        $response = $this->actingAs($customer)->get(route('orders.invoice', $order));
        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_customer_cannot_download_invoice_for_unpaid_order(): void
    {
        $customer = Customer::factory()->create();
        $order = Order::factory()->create(['customer_id' => $customer->id, 'payment_status' => 'pending']);

        $response = $this->actingAs($customer)->get(route('orders.invoice', $order));
        $response->assertRedirect();
    }

    public function test_customer_cannot_download_invoice_for_another_customers_order(): void
    {
        $customer1 = Customer::factory()->create();
        $customer2 = Customer::factory()->create();
        $order = Order::factory()->paid()->create(['customer_id' => $customer1->id]);

        $response = $this->actingAs($customer2)->get(route('orders.invoice', $order));
        $response->assertForbidden();
    }

    public function test_guest_cannot_download_invoice(): void
    {
        $order = Order::factory()->paid()->create();

        $response = $this->get(route('orders.invoice', $order));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_download_any_invoice(): void
    {
        $admin = Customer::factory()->create(['role' => 'admin', 'is_admin' => true]);
        $order = Order::factory()->paid()->create();

        $response = $this->actingAs($admin)->get(route('admin.orders.invoice', $order));
        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_invoice_pdf_contains_correct_filename(): void
    {
        $customer = Customer::factory()->create();
        $order = Order::factory()->paid()->create(['customer_id' => $customer->id]);

        $response = $this->actingAs($customer)->get(route('orders.invoice', $order));
        $response->assertOk();
        $disposition = $response->headers->get('content-disposition');
        $this->assertStringContainsString('attachment', $disposition);
        $this->assertStringContainsString('invoice-' . $order->id . '.pdf', $disposition);
    }
}
