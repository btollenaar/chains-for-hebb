<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewSystemTest extends TestCase
{
    use RefreshDatabase;

    protected Customer $customer;
    protected Customer $admin;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Create customers
        $this->customer = Customer::factory()->create();
        $this->admin = Customer::factory()->create([
            'role' => 'admin',
            'is_admin' => true,
        ]);

        // Create test product
        $this->product = Product::factory()->create();
    }

    /**
     * Test 1: Customer can submit product review
     * Tests basic review submission workflow
     */
    public function test_customer_can_submit_product_review(): void
    {
        // Arrange
        $this->actingAs($this->customer);

        // Act: Submit review
        $response = $this->post(route('products.reviews.store', $this->product), [
            'rating' => 5,
            'title' => 'Excellent Product!',
            'comment' => 'This product exceeded my expectations.',
        ]);

        // Assert: Redirect back with success
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Assert: Review created with pending status
        $this->assertDatabaseHas('reviews', [
            'customer_id' => $this->customer->id,
            'reviewable_type' => Product::class,
            'reviewable_id' => $this->product->id,
            'rating' => 5,
            'title' => 'Excellent Product!',
            'status' => 'pending', // Awaits moderation
            'verified_purchase' => false, // No order yet
        ]);
    }

    /**
     * Test 2: Verified purchase badge for confirmed buyers
     * Tests automatic verification based on paid orders
     */
    public function test_verified_purchase_badge_for_confirmed_buyers(): void
    {
        // Arrange
        $this->actingAs($this->customer);

        // Create paid order with product
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'payment_status' => 'paid',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'item_type' => Product::class,
            'item_id' => $this->product->id,
            'name' => $this->product->name, // Required field
            'quantity' => 1,
            'unit_price' => 99.99,
            'subtotal' => 99.99,
            'total' => 99.99,
        ]);

        // Act: Submit review
        $this->post(route('products.reviews.store', $this->product), [
            'rating' => 4,
            'title' => 'Great purchase',
            'comment' => 'Very satisfied with this product.',
        ]);

        // Assert: Review marked as verified purchase
        $this->assertDatabaseHas('reviews', [
            'customer_id' => $this->customer->id,
            'reviewable_id' => $this->product->id,
            'verified_purchase' => true, // Auto-detected from order
        ]);
    }

    /**
     * Test 3: Duplicate review prevention
     * Ensures one review per item per customer
     */
    public function test_duplicate_review_prevention(): void
    {
        // Arrange
        $this->actingAs($this->customer);

        // Create existing review
        Review::create([
            'customer_id' => $this->customer->id,
            'reviewable_type' => Product::class,
            'reviewable_id' => $this->product->id,
            'rating' => 5,
            'status' => 'approved',
        ]);

        // Act: Try to submit duplicate review
        $response = $this->post(route('products.reviews.store', $this->product), [
            'rating' => 3,
            'title' => 'Changed my mind',
            'comment' => 'Actually not that great.',
        ]);

        // Assert: Redirect with error
        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Assert: Only one review exists
        $this->assertEquals(1, Review::where('customer_id', $this->customer->id)
            ->where('reviewable_id', $this->product->id)
            ->count());
    }

    /**
     * Test 4: Admin can approve review
     * Tests admin moderation approval workflow
     */
    public function test_admin_can_approve_review(): void
    {
        // Arrange
        $this->actingAs($this->admin);

        $review = Review::factory()->create([
            'customer_id' => $this->customer->id,
            'reviewable_type' => Product::class,
            'reviewable_id' => $this->product->id,
            'status' => 'pending',
        ]);

        // Act: Approve review
        $response = $this->post(route('admin.reviews.approve', $review));

        // Assert: Redirect with success
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Assert: Review status changed
        $review->refresh();
        $this->assertEquals('approved', $review->status);
        $this->assertTrue($review->is_approved);
    }

    /**
     * Test 5: Admin can reject review
     * Tests admin moderation rejection workflow
     */
    public function test_admin_can_reject_review(): void
    {
        // Arrange
        $this->actingAs($this->admin);

        $review = Review::factory()->create([
            'customer_id' => $this->customer->id,
            'reviewable_type' => Product::class,
            'reviewable_id' => $this->product->id,
            'status' => 'pending',
        ]);

        // Act: Reject review
        $response = $this->post(route('admin.reviews.reject', $review));

        // Assert: Redirect with success
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Assert: Review status changed
        $review->refresh();
        $this->assertEquals('rejected', $review->status);
    }

    /**
     * Test 6: Admin can add response to review
     * Tests public admin response functionality
     */
    public function test_admin_can_add_response(): void
    {
        // Arrange
        $this->actingAs($this->admin);

        $review = Review::factory()->create([
            'customer_id' => $this->customer->id,
            'reviewable_type' => Product::class,
            'reviewable_id' => $this->product->id,
            'status' => 'approved',
        ]);

        // Act: Add admin response
        $response = $this->put(route('admin.reviews.update', $review), [
            'admin_response' => 'Thank you for your feedback! We appreciate your business.',
        ]);

        // Assert: Redirect with success
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Assert: Response saved
        $review->refresh();
        $this->assertEquals('Thank you for your feedback! We appreciate your business.', $review->admin_response);
        $this->assertNotNull($review->responded_at);
    }

    /**
     * Test 7: Helpful voting increments count
     * Tests customer engagement with reviews
     */
    public function test_helpful_voting_increments_count(): void
    {
        // Arrange: Create a different customer who authored the review
        $reviewAuthor = Customer::factory()->create();

        $review = Review::factory()->create([
            'customer_id' => $reviewAuthor->id, // Review by different customer
            'reviewable_type' => Product::class,
            'reviewable_id' => $this->product->id,
            'status' => 'approved',
            'helpful_count' => 0,
            'not_helpful_count' => 0,
        ]);

        $reviewId = $review->id;

        // Act as a different customer (not the author) to vote
        $this->actingAs($this->customer);

        // Act: Mark as helpful
        $response = $this->post(route('reviews.helpful', $review));

        // Assert: Redirect with success
        $response->assertStatus(302); // Redirect back
        $response->assertSessionHas('success');

        // Assert: Helpful count incremented in database
        $this->assertDatabaseHas('reviews', [
            'id' => $reviewId,
            'helpful_count' => 1,
        ]);

        // Act: Mark as not helpful
        $response = $this->post(route('reviews.not-helpful', $review));

        // Assert: Response successful
        $response->assertStatus(302);
        $response->assertSessionHas('success');

        // Assert: Not helpful count incremented in database
        $this->assertDatabaseHas('reviews', [
            'id' => $reviewId,
            'not_helpful_count' => 1,
        ]);
    }

    /**
     * Test 8: Review moderation filters work
     * Tests admin filter functionality
     */
    public function test_review_moderation_filters_work(): void
    {
        // Arrange
        $this->actingAs($this->admin);

        // Create reviews with different statuses
        Review::factory()->create(['status' => 'pending', 'rating' => 5]);
        Review::factory()->create(['status' => 'approved', 'rating' => 4]);
        Review::factory()->create(['status' => 'rejected', 'rating' => 2]);
        Review::factory()->create([
            'status' => 'approved',
            'rating' => 5,
            'verified_purchase' => true,
        ]);

        // Act & Assert: Filter by status
        $response = $this->get(route('admin.reviews.index', ['status' => 'pending']));
        $response->assertStatus(200);
        $response->assertSee('pending'); // At least one pending review

        // Act & Assert: Filter by rating
        $response = $this->get(route('admin.reviews.index', ['rating' => 5]));
        $response->assertStatus(200);

        // Act & Assert: Filter by verified
        $response = $this->get(route('admin.reviews.index', ['verified' => 'yes']));
        $response->assertStatus(200);

        // Act & Assert: Filter by type
        $productReview = Review::factory()->create([
            'reviewable_type' => Product::class,
            'reviewable_id' => $this->product->id,
        ]);

        $response = $this->get(route('admin.reviews.index', ['type' => 'products']));
        $response->assertStatus(200);
    }

    /**
     * Test 10: Related reviews shown in admin detail view
     * Tests context-aware review management
     */
    public function test_related_reviews_shown_in_admin(): void
    {
        // Arrange
        $this->actingAs($this->admin);

        // Create main review
        $mainReview = Review::factory()->create([
            'reviewable_type' => Product::class,
            'reviewable_id' => $this->product->id,
            'status' => 'pending',
        ]);

        // Create related reviews for same product
        Review::factory()->count(3)->create([
            'reviewable_type' => Product::class,
            'reviewable_id' => $this->product->id,
            'status' => 'approved',
        ]);

        // Create unrelated review (different product)
        $otherProduct = Product::factory()->create();
        Review::factory()->create([
            'reviewable_type' => Product::class,
            'reviewable_id' => $otherProduct->id,
            'status' => 'approved',
        ]);

        // Act: View review detail page
        $response = $this->get(route('admin.reviews.show', $mainReview));

        // Assert: Page loads successfully
        $response->assertStatus(200);

        // Assert: Related reviews are passed to view
        $response->assertViewHas('relatedReviews');

        // Verify related reviews are for same product
        $relatedReviews = $response->viewData('relatedReviews');
        $this->assertNotNull($relatedReviews);

        foreach ($relatedReviews as $related) {
            $this->assertEquals($this->product->id, $related->reviewable_id);
            $this->assertNotEquals($mainReview->id, $related->id);
        }
    }
}
