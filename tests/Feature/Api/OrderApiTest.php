<?php

namespace Tests\Feature\Api;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_list_own_orders(): void
    {
        $customer = Customer::factory()->create();
        Order::factory()->count(2)->create(['customer_id' => $customer->id]);

        $response = $this->actingAs($customer, 'sanctum')
            ->getJson('/api/v1/orders');

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
    }

    public function test_unauthenticated_user_cannot_list_orders(): void
    {
        $response = $this->getJson('/api/v1/orders');
        $response->assertUnauthorized();
    }

    public function test_user_cannot_view_other_users_order(): void
    {
        $customer1 = Customer::factory()->create();
        $customer2 = Customer::factory()->create();
        $order = Order::factory()->create(['customer_id' => $customer1->id]);

        $response = $this->actingAs($customer2, 'sanctum')
            ->getJson("/api/v1/orders/{$order->id}");

        $response->assertForbidden();
    }

    public function test_user_can_view_own_order(): void
    {
        $customer = Customer::factory()->create();
        $order = Order::factory()->create(['customer_id' => $customer->id]);

        $response = $this->actingAs($customer, 'sanctum')
            ->getJson("/api/v1/orders/{$order->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $order->id)
            ->assertJsonPath('data.order_number', $order->order_number);
    }

    public function test_order_response_includes_items_when_loaded(): void
    {
        $customer = Customer::factory()->create();
        $order = Order::factory()->withItems(2)->create(['customer_id' => $customer->id]);

        $response = $this->actingAs($customer, 'sanctum')
            ->getJson("/api/v1/orders/{$order->id}");

        $response->assertOk()
            ->assertJsonCount(2, 'data.items');
    }

    public function test_orders_are_returned_in_descending_order(): void
    {
        $customer = Customer::factory()->create();
        $older = Order::factory()->create([
            'customer_id' => $customer->id,
            'created_at' => now()->subDays(5),
        ]);
        $newer = Order::factory()->create([
            'customer_id' => $customer->id,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($customer, 'sanctum')
            ->getJson('/api/v1/orders');

        $response->assertOk();
        $this->assertEquals($newer->id, $response->json('data.0.id'));
        $this->assertEquals($older->id, $response->json('data.1.id'));
    }
}
