<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistSharingTest extends TestCase
{
    use RefreshDatabase;

    protected Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = Customer::factory()->create();
    }

    /**
     * Test 1: Authenticated user can generate a share token
     * POST /wishlist/share returns JSON with a share URL
     */
    public function test_authenticated_user_can_generate_share_token(): void
    {
        // Arrange
        $this->actingAs($this->customer);

        // Act
        $response = $this->postJson(route('wishlist.share'));

        // Assert
        $response->assertOk();
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure(['success', 'url']);

        // Token should now be persisted on the customer
        $this->customer->refresh();
        $this->assertNotNull($this->customer->wishlist_share_token);
        $this->assertEquals(64, strlen($this->customer->wishlist_share_token));

        // The returned URL should contain the token
        $this->assertStringContainsString($this->customer->wishlist_share_token, $response->json('url'));
    }

    /**
     * Test 2: Share token is created on demand (initially null)
     * A newly created customer has no share token until they request one
     */
    public function test_share_token_is_created_on_demand(): void
    {
        // Assert: Token is null before sharing
        $this->assertNull($this->customer->wishlist_share_token);

        // Act: Request a share link
        $this->actingAs($this->customer);
        $this->postJson(route('wishlist.share'));

        // Assert: Token is now set
        $this->customer->refresh();
        $this->assertNotNull($this->customer->wishlist_share_token);
        $this->assertDatabaseHas('customers', [
            'id' => $this->customer->id,
            'wishlist_share_token' => $this->customer->wishlist_share_token,
        ]);
    }

    /**
     * Test 3: Calling share again returns the same token
     * The token is stable once generated -- not regenerated on each call
     */
    public function test_calling_share_again_returns_same_token(): void
    {
        // Arrange
        $this->actingAs($this->customer);

        // Act: First call generates the token
        $response1 = $this->postJson(route('wishlist.share'));
        $url1 = $response1->json('url');

        // Act: Second call returns the same token
        $response2 = $this->postJson(route('wishlist.share'));
        $url2 = $response2->json('url');

        // Assert: Both URLs are identical
        $this->assertEquals($url1, $url2);

        // Assert: Token in database has not changed
        $this->customer->refresh();
        $this->assertStringContainsString($this->customer->wishlist_share_token, $url1);
    }

    /**
     * Test 4: Public shared wishlist view loads with valid token
     * GET /wishlist/shared/{token} returns 200 for a valid token
     */
    public function test_shared_wishlist_loads_with_valid_token(): void
    {
        // Arrange: Set a share token on the customer
        $token = str_repeat('a', 64);
        $this->customer->update(['wishlist_share_token' => $token]);

        // Act: Visit the shared wishlist as a guest (no authentication)
        $response = $this->get(route('wishlist.shared', $token));

        // Assert
        $response->assertOk();
        $response->assertSee($this->customer->name . "'s Wishlist");
    }

    /**
     * Test 5: Invalid token returns 404
     * GET /wishlist/shared/{token} with a non-existent token returns 404
     */
    public function test_invalid_token_returns_404(): void
    {
        // Act
        $response = $this->get(route('wishlist.shared', 'nonexistent-token-that-does-not-match-anyone'));

        // Assert
        $response->assertNotFound();
    }

    /**
     * Test 6: Shared wishlist displays items
     * The shared view shows products that are in the customer's wishlist
     */
    public function test_shared_wishlist_displays_items(): void
    {
        // Arrange: Create products and add them to the wishlist
        $product1 = Product::factory()->create(['name' => 'Cool T-Shirt', 'status' => 'active']);
        $product2 = Product::factory()->create(['name' => 'Awesome Mug', 'status' => 'active']);

        Wishlist::create([
            'customer_id' => $this->customer->id,
            'item_type' => Product::class,
            'item_id' => $product1->id,
        ]);

        Wishlist::create([
            'customer_id' => $this->customer->id,
            'item_type' => Product::class,
            'item_id' => $product2->id,
        ]);

        // Set a share token
        $token = str_repeat('b', 64);
        $this->customer->update(['wishlist_share_token' => $token]);

        // Act
        $response = $this->get(route('wishlist.shared', $token));

        // Assert: Both products are visible on the shared page
        $response->assertOk();
        $response->assertSee('Cool T-Shirt');
        $response->assertSee('Awesome Mug');
        $response->assertSee('2 items');
    }

    /**
     * Test 7: Unauthenticated user cannot generate share token
     * POST /wishlist/share without authentication redirects to login
     */
    public function test_unauthenticated_user_cannot_generate_share_token(): void
    {
        // Act: Attempt to share without being logged in
        $response = $this->post(route('wishlist.share'));

        // Assert: Redirected to login
        $response->assertRedirect(route('login'));
    }
}
