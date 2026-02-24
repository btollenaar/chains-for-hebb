<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Newsletter;
use App\Models\NewsletterSend;
use App\Models\NewsletterSubscription;
use App\Models\SubscriberList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsletterUnsubscribeTest extends TestCase
{
    use RefreshDatabase;

    protected Newsletter $newsletter;
    protected NewsletterSubscription $subscription;
    protected NewsletterSend $send;
    protected SubscriberList $list;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin for newsletter
        $admin = Customer::factory()->create([
            'role' => 'admin',
            'is_admin' => true,
        ]);

        // Create a subscriber list
        $this->list = SubscriberList::factory()->create([
            'name' => 'Test List',
            'is_system' => false,
        ]);

        // Create newsletter
        $this->newsletter = Newsletter::factory()->create([
            'created_by' => $admin->id,
            'status' => 'sent',
        ]);

        // Create active subscription
        $this->subscription = NewsletterSubscription::factory()->create([
            'is_active' => true,
            'email' => 'subscriber@example.com',
        ]);

        // Attach to list
        $this->subscription->lists()->attach($this->list->id);

        // Create send record with tracking token
        $this->send = NewsletterSend::factory()->create([
            'newsletter_id' => $this->newsletter->id,
            'newsletter_subscription_id' => $this->subscription->id,
            'status' => 'sent',
        ]);
    }

    /**
     * Test 1: Unsubscribe page shows with valid token
     */
    public function test_unsubscribe_page_shows_with_valid_token(): void
    {
        // Act: Visit unsubscribe page
        $response = $this->get(route('newsletter.unsubscribe', [
            'token' => $this->send->tracking_token,
        ]));

        // Assert: Page loads with email
        $response->assertStatus(200);
        $response->assertViewIs('newsletter.unsubscribe');
        $response->assertSee($this->subscription->email);
    }

    /**
     * Test 2: Unsubscribe page shows error with invalid token
     */
    public function test_unsubscribe_page_shows_error_with_invalid_token(): void
    {
        // Act: Visit with invalid token
        $response = $this->get(route('newsletter.unsubscribe', [
            'token' => 'invalid-token-12345',
        ]));

        // Assert: Error view shown
        $response->assertStatus(200);
        $response->assertViewIs('newsletter.unsubscribe-error');
    }

    /**
     * Test 3: Unsubscribe page shows error without token
     */
    public function test_unsubscribe_page_shows_error_without_token(): void
    {
        // Act: Visit without token
        $response = $this->get(route('newsletter.unsubscribe'));

        // Assert: Error view shown
        $response->assertStatus(200);
        $response->assertViewIs('newsletter.unsubscribe-error');
    }

    /**
     * Test 4: Processing unsubscribe deactivates subscription
     */
    public function test_processing_unsubscribe_deactivates_subscription(): void
    {
        // Arrange: Confirm active
        $this->assertTrue($this->subscription->is_active);

        // Act: Submit unsubscribe
        $response = $this->post(route('newsletter.unsubscribe'), [
            'token' => $this->send->tracking_token,
        ]);

        // Assert: Success view
        $response->assertStatus(200);
        $response->assertViewIs('newsletter.unsubscribed');

        // Assert: Subscription deactivated
        $this->subscription->refresh();
        $this->assertFalse($this->subscription->is_active);
        $this->assertNotNull($this->subscription->unsubscribed_at);
    }

    /**
     * Test 5: Unsubscribe removes from all lists
     */
    public function test_unsubscribe_removes_from_all_lists(): void
    {
        // Arrange: Create and attach additional list
        $secondList = SubscriberList::factory()->create();
        $this->subscription->lists()->attach($secondList->id);

        // Verify attached to lists
        $this->assertEquals(2, $this->subscription->lists()->count());

        // Act: Unsubscribe
        $this->post(route('newsletter.unsubscribe'), [
            'token' => $this->send->tracking_token,
        ]);

        // Assert: Removed from all lists
        $this->subscription->refresh();
        $this->assertEquals(0, $this->subscription->lists()->count());
    }

    /**
     * Test 6: Already unsubscribed shows appropriate message
     */
    public function test_already_unsubscribed_shows_appropriate_message(): void
    {
        // Arrange: Deactivate subscription
        $this->subscription->update([
            'is_active' => false,
            'unsubscribed_at' => now(),
        ]);

        // Act: Visit unsubscribe page
        $response = $this->get(route('newsletter.unsubscribe', [
            'token' => $this->send->tracking_token,
        ]));

        // Assert: Already unsubscribed view
        $response->assertStatus(200);
        $response->assertViewIs('newsletter.already-unsubscribed');
    }

    /**
     * Test 7: Unsubscribe with invalid token on POST
     */
    public function test_unsubscribe_post_with_invalid_token(): void
    {
        // Act: Submit with invalid token
        $response = $this->post(route('newsletter.unsubscribe'), [
            'token' => 'invalid-token',
        ]);

        // Assert: Error view
        $response->assertStatus(200);
        $response->assertViewIs('newsletter.unsubscribe-error');
    }

    /**
     * Test 8: Unsubscribe without token on POST
     */
    public function test_unsubscribe_post_without_token(): void
    {
        // Act: Submit without token
        $response = $this->post(route('newsletter.unsubscribe'), []);

        // Assert: Redirect with error
        $response->assertRedirect();
    }

    /**
     * Test 9: Unsubscribe is logged
     */
    public function test_unsubscribe_is_idempotent(): void
    {
        // Act: Unsubscribe once
        $this->post(route('newsletter.unsubscribe'), [
            'token' => $this->send->tracking_token,
        ]);

        // Verify unsubscribed
        $this->subscription->refresh();
        $firstUnsubscribeTime = $this->subscription->unsubscribed_at;

        // Act: Try to access unsubscribe page again
        $response = $this->get(route('newsletter.unsubscribe', [
            'token' => $this->send->tracking_token,
        ]));

        // Assert: Shows already unsubscribed
        $response->assertViewIs('newsletter.already-unsubscribed');
    }
}
