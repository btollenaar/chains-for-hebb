<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CartOwnershipTest extends TestCase
{
    use RefreshDatabase;

    protected function makeProduct(): Product
    {
        $name = 'Test Product ' . Str::random(5);

        return Product::create([
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(5),
            'description' => 'Test',
            'sku' => Str::upper(Str::random(8)),
            'price' => 25,
            'stock_quantity' => 10,
            'category' => 'test',
            'status' => 'active',
        ]);
    }

    public function test_customer_cannot_update_another_customers_cart_item(): void
    {
        $product = $this->makeProduct();
        $owner = Customer::factory()->create();
        $other = Customer::factory()->create();

        $ownerItem = Cart::create([
            'customer_id' => $owner->id,
            'item_type' => Product::class,
            'item_id' => $product->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($other)->patch(route('cart.update', $ownerItem->id), [
            'quantity' => 5,
        ]);

        $response->assertStatus(404); // findForOwner returns null, aborts with 404
        $this->assertDatabaseHas('cart', [
            'id' => $ownerItem->id,
            'quantity' => 1, // Unchanged
        ]);
    }

    public function test_customer_cannot_delete_another_customers_cart_item(): void
    {
        $product = $this->makeProduct();
        $owner = Customer::factory()->create();
        $other = Customer::factory()->create();

        $ownerItem = Cart::create([
            'customer_id' => $owner->id,
            'item_type' => Product::class,
            'item_id' => $product->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($other)->delete(route('cart.remove', $ownerItem->id));

        $response->assertStatus(404);
        $this->assertDatabaseHas('cart', ['id' => $ownerItem->id]);
    }
}
