<?php

namespace Tests\Feature;

use App\Mail\BackInStockMail;
use App\Models\Customer;
use App\Models\Product;
use App\Models\StockNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class StockNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_subscribe_to_out_of_stock_notification(): void
    {
        $product = Product::factory()->outOfStock()->create();

        $response = $this->postJson(route('stock-notifications.store'), [
            'product_id' => $product->id,
            'email' => 'test@example.com',
        ]);

        $response->assertOk()
            ->assertJson(['message' => "You'll be notified when this product is back in stock!"]);

        $this->assertDatabaseHas('stock_notifications', [
            'email' => 'test@example.com',
            'product_id' => $product->id,
            'notified_at' => null,
        ]);
    }

    public function test_cannot_subscribe_to_in_stock_product(): void
    {
        $product = Product::factory()->inStock(50)->create();

        $response = $this->postJson(route('stock-notifications.store'), [
            'product_id' => $product->id,
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJson(['message' => 'This product is currently in stock.']);

        $this->assertDatabaseMissing('stock_notifications', [
            'email' => 'test@example.com',
            'product_id' => $product->id,
        ]);
    }

    public function test_duplicate_subscription_updates_existing(): void
    {
        $product = Product::factory()->outOfStock()->create();

        $this->postJson(route('stock-notifications.store'), [
            'product_id' => $product->id,
            'email' => 'test@example.com',
        ]);

        $this->postJson(route('stock-notifications.store'), [
            'product_id' => $product->id,
            'email' => 'test@example.com',
        ]);

        $this->assertEquals(1, StockNotification::where('email', 'test@example.com')
            ->where('product_id', $product->id)
            ->count());
    }

    public function test_authenticated_user_email_prefilled(): void
    {
        $customer = Customer::factory()->create(['email' => 'customer@example.com']);
        $product = Product::factory()->outOfStock()->create();

        $response = $this->actingAs($customer)->postJson(route('stock-notifications.store'), [
            'product_id' => $product->id,
            'email' => 'customer@example.com',
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('stock_notifications', [
            'email' => 'customer@example.com',
            'product_id' => $product->id,
            'customer_id' => $customer->id,
        ]);
    }

    public function test_notification_command_sends_emails_for_restocked(): void
    {
        Mail::fake();

        $product = Product::factory()->inStock(10)->create(['status' => 'active']);

        $notification = StockNotification::create([
            'email' => 'waiting@example.com',
            'product_id' => $product->id,
            'notified_at' => null,
        ]);

        $this->artisan('notifications:send-back-in-stock')
            ->assertExitCode(0);

        Mail::assertSent(BackInStockMail::class, function (BackInStockMail $mail) use ($product) {
            return $mail->product->id === $product->id
                && $mail->hasTo('waiting@example.com');
        });

        $notification->refresh();
        $this->assertNotNull($notification->notified_at);
    }
}
