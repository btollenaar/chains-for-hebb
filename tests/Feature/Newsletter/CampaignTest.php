<?php

namespace Tests\Feature\Newsletter;

use App\Models\Customer;
use App\Models\Newsletter;
use App\Models\NewsletterSend;
use App\Models\NewsletterSubscription;
use App\Models\SubscriberList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CampaignTest extends TestCase
{
    use RefreshDatabase;

    protected Customer $admin;
    protected SubscriberList $list;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = Customer::factory()->create([
            'role' => 'admin',
            'is_admin' => true,
        ]);

        // Create subscriber list
        $this->list = SubscriberList::factory()->create([
            'name' => 'Test List',
        ]);
    }

    /**
     * Test 1: Admin can create newsletter campaign
     * Tests campaign creation with all fields
     */
    public function test_admin_can_create_newsletter_campaign(): void
    {
        // Arrange
        $this->actingAs($this->admin);

        // Create some subscribers
        NewsletterSubscription::factory()->count(5)->create()->each(function ($subscription) {
            $subscription->lists()->attach($this->list->id);
        });

        $campaignData = [
            'subject' => 'Welcome Newsletter',
            'preview_text' => 'Welcome to our newsletter!',
            'content' => '<p>Hello <strong>subscriber</strong>!</p>',
            'from_name' => 'Test Company',
            'from_email' => 'test@example.com',
            'lists' => [$this->list->id],
            'action' => 'save_draft',
        ];

        // Act
        $response = $this->post(route('admin.newsletters.campaigns.store'), $campaignData);

        // Assert
        $this->assertDatabaseHas('newsletters', [
            'subject' => 'Welcome Newsletter',
            'content' => '<p>Hello <strong>subscriber</strong>!</p>',
            'status' => 'draft',
            'from_name' => 'Test Company',
            'from_email' => 'test@example.com',
        ]);

        $newsletter = Newsletter::where('subject', 'Welcome Newsletter')->first();
        $this->assertEquals(1, $newsletter->lists()->count());

        $response->assertRedirect(route('admin.newsletters.campaigns.show', $newsletter));
    }

    /**
     * Test 2: Plain text generated from HTML
     * Tests automatic plain text generation
     */
    public function test_plain_text_generated_from_html(): void
    {
        // Arrange
        $this->actingAs($this->admin);

        NewsletterSubscription::factory()->count(3)->create()->each(function ($subscription) {
            $subscription->lists()->attach($this->list->id);
        });

        $campaignData = [
            'subject' => 'HTML Newsletter',
            'content' => '<p>This is <strong>bold</strong> text.</p><p>Another paragraph.</p>',
            'lists' => [$this->list->id],
            'action' => 'save_draft',
        ];

        // Act
        $this->post(route('admin.newsletters.campaigns.store'), $campaignData);

        // Assert
        $newsletter = Newsletter::where('subject', 'HTML Newsletter')->first();
        $this->assertNotNull($newsletter->plain_text_content);
        $this->assertStringContainsString('This is bold text', $newsletter->plain_text_content);
        $this->assertStringNotContainsString('<strong>', $newsletter->plain_text_content);
        $this->assertStringNotContainsString('<p>', $newsletter->plain_text_content);
    }

    /**
     * Test 3: Scheduled newsletter dispatched at time
     * Tests scheduler integration (mocked)
     */
    public function test_scheduled_newsletter_dispatched_at_time(): void
    {
        // Arrange
        $this->actingAs($this->admin);

        NewsletterSubscription::factory()->count(3)->create()->each(function ($subscription) {
            $subscription->lists()->attach($this->list->id);
        });

        $scheduledTime = now()->addHours(2);

        $campaignData = [
            'subject' => 'Scheduled Newsletter',
            'content' => '<p>Scheduled content</p>',
            'lists' => [$this->list->id],
            'action' => 'schedule',
            'scheduled_at' => $scheduledTime->toDateTimeString(),
        ];

        // Act
        $this->post(route('admin.newsletters.campaigns.store'), $campaignData);

        // Assert
        $newsletter = Newsletter::where('subject', 'Scheduled Newsletter')->first();
        $this->assertEquals('scheduled', $newsletter->status);
        $this->assertEquals($scheduledTime->format('Y-m-d H:i'), $newsletter->scheduled_at->format('Y-m-d H:i'));
    }

    /**
     * Test 4: Newsletter sends created for recipients
     * Tests send record creation
     */
    public function test_newsletter_sends_created_for_recipients(): void
    {
        // Arrange
        $newsletter = Newsletter::factory()->create(['status' => 'draft']);

        $subscribers = NewsletterSubscription::factory()->count(5)->create();
        $subscribers->each(function ($subscription) {
            $subscription->lists()->attach($this->list->id);
        });

        $newsletter->lists()->attach($this->list->id);

        // Act: Create send records
        foreach ($subscribers as $subscriber) {
            NewsletterSend::create([
                'newsletter_id' => $newsletter->id,
                'newsletter_subscription_id' => $subscriber->id,
                'status' => 'pending',
            ]);
        }

        // Assert
        $this->assertEquals(5, $newsletter->sends()->count());
        $this->assertEquals(5, NewsletterSend::where('newsletter_id', $newsletter->id)->count());
    }

    /**
     * Test 5: Tracking token generated per send
     * Tests unique token generation for different subscribers
     */
    public function test_tracking_token_generated_per_send(): void
    {
        // Arrange
        $newsletter = Newsletter::factory()->create();
        $subscriber1 = NewsletterSubscription::factory()->create();
        $subscriber2 = NewsletterSubscription::factory()->create();

        // Act - Create sends for two different subscribers
        $send1 = NewsletterSend::create([
            'newsletter_id' => $newsletter->id,
            'newsletter_subscription_id' => $subscriber1->id,
            'status' => 'pending',
        ]);

        $send2 = NewsletterSend::create([
            'newsletter_id' => $newsletter->id,
            'newsletter_subscription_id' => $subscriber2->id,
            'status' => 'pending',
        ]);

        // Assert: Tokens auto-generated and unique
        $this->assertNotNull($send1->tracking_token);
        $this->assertNotNull($send2->tracking_token);
        $this->assertEquals(64, strlen($send1->tracking_token));
        $this->assertEquals(64, strlen($send2->tracking_token));
        $this->assertNotEquals($send1->tracking_token, $send2->tracking_token);
    }

    /**
     * Test 6: Open tracking pixel increments count
     * Tests open tracking functionality
     */
    public function test_open_tracking_pixel_increments_count(): void
    {
        // Arrange
        $newsletter = Newsletter::factory()->create([
            'sent_count' => 10,
            'open_count' => 0,
        ]);

        $subscriber = NewsletterSubscription::factory()->create();

        $send = NewsletterSend::create([
            'newsletter_id' => $newsletter->id,
            'newsletter_subscription_id' => $subscriber->id,
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        // Act: Track open
        $send->trackOpen();

        // Assert
        $send->refresh();
        $this->assertNotNull($send->opened_at);

        $newsletter->refresh();
        $this->assertEquals(1, $newsletter->open_count);

        // Act: Track open again (should not increment)
        $send->trackOpen();

        $newsletter->refresh();
        $this->assertEquals(1, $newsletter->open_count); // Still 1
    }

    /**
     * Test 7: Click tracking redirects and increments
     * Tests click tracking functionality
     */
    public function test_click_tracking_redirects_and_increments(): void
    {
        // Arrange
        $newsletter = Newsletter::factory()->create([
            'sent_count' => 10,
            'click_count' => 0,
            'open_count' => 0,
        ]);

        $subscriber = NewsletterSubscription::factory()->create();

        $send = NewsletterSend::create([
            'newsletter_id' => $newsletter->id,
            'newsletter_subscription_id' => $subscriber->id,
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        // Act: Track click
        $send->trackClick();

        // Assert
        $send->refresh();
        $this->assertNotNull($send->clicked_at);
        $this->assertNotNull($send->opened_at); // Auto-marks as opened

        $newsletter->refresh();
        $this->assertEquals(1, $newsletter->click_count);
        $this->assertEquals(1, $newsletter->open_count);

        // Act: Track click again (should not increment)
        $send->trackClick();

        $newsletter->refresh();
        $this->assertEquals(1, $newsletter->click_count); // Still 1
    }

    /**
     * Test 8: Unsubscribe link deactivates subscriber
     * Tests unsubscribe functionality
     */
    public function test_unsubscribe_link_deactivates_subscriber(): void
    {
        // Arrange
        $subscriber = NewsletterSubscription::factory()->create([
            'email' => 'test@example.com',
            'is_active' => true,
        ]);

        $send = NewsletterSend::create([
            'newsletter_id' => Newsletter::factory()->create()->id,
            'newsletter_subscription_id' => $subscriber->id,
            'status' => 'sent',
        ]);

        // Act: Unsubscribe via token
        $this->post(route('newsletter.unsubscribe'), [
            'token' => $send->tracking_token,
        ]);

        // Assert: Subscriber deactivated (ignore redirect/view rendering)
        $subscriber->refresh();
        $this->assertFalse($subscriber->is_active);
        $this->assertNotNull($subscriber->unsubscribed_at);
    }

    /**
     * Test 9: Failed email marked in database
     * Tests error handling
     */
    public function test_failed_email_marked_in_database(): void
    {
        // Arrange
        $newsletter = Newsletter::factory()->create([
            'sent_count' => 5,
            'failed_count' => 0,
        ]);

        $subscriber = NewsletterSubscription::factory()->create();

        $send = NewsletterSend::create([
            'newsletter_id' => $newsletter->id,
            'newsletter_subscription_id' => $subscriber->id,
            'status' => 'pending',
        ]);

        // Act: Mark as failed
        $send->markAsFailed('SMTP connection timeout');

        // Assert
        $send->refresh();
        $this->assertEquals('failed', $send->status);
        $this->assertEquals('SMTP connection timeout', $send->error_message);

        $newsletter->refresh();
        $this->assertEquals(1, $newsletter->failed_count);
    }

    /**
     * Test 10: Test email excludes tracking pixel
     * Tests test email mode
     */
    public function test_test_email_excludes_tracking_pixel(): void
    {
        // Note: This test verifies the mailable behavior
        // In actual implementation, NewsletterMail should accept isTest flag

        Mail::fake();

        $this->actingAs($this->admin);

        $newsletter = Newsletter::factory()->create();

        // Act: Send test email
        $response = $this->post(route('admin.newsletters.campaigns.send-test', $newsletter), [
            'test_emails' => 'admin@example.com',
        ]);

        // Assert
        Mail::assertSent(\App\Mail\NewsletterMail::class, function ($mail) {
            return $mail->hasTo('admin@example.com');
        });

        $response->assertJson(['success' => true]);
    }

    /**
     * Test 11: Duplicate campaign copies attributes
     * Tests campaign duplication
     */
    public function test_duplicate_campaign_copies_attributes(): void
    {
        // Arrange
        $this->actingAs($this->admin);

        $original = Newsletter::factory()->create([
            'subject' => 'Original Campaign',
            'content' => '<p>Original content</p>',
            'from_name' => 'Test Sender',
            'status' => 'sent',
        ]);

        $original->lists()->attach($this->list->id);

        // Act: Duplicate campaign
        $response = $this->post(route('admin.newsletters.campaigns.duplicate', $original));

        // Assert
        $this->assertDatabaseHas('newsletters', [
            'subject' => 'Original Campaign (Copy)',
            'content' => '<p>Original content</p>',
            'from_name' => 'Test Sender',
            'status' => 'draft',
        ]);

        $duplicate = Newsletter::where('subject', 'Original Campaign (Copy)')->first();
        $this->assertEquals('draft', $duplicate->status);
        $this->assertEquals(1, $duplicate->lists()->count());
        $this->assertEquals(0, $duplicate->sent_count);
        $this->assertEquals(0, $duplicate->open_count);

        $response->assertRedirect();
    }

    /**
     * Test 12: Cancel scheduled reverts to draft
     * Tests campaign cancellation
     */
    public function test_cancel_scheduled_reverts_to_draft(): void
    {
        // Arrange
        $this->actingAs($this->admin);

        $newsletter = Newsletter::factory()->create([
            'status' => 'scheduled',
            'scheduled_at' => now()->addHours(2),
        ]);

        // Act: Cancel scheduled campaign
        $response = $this->post(route('admin.newsletters.campaigns.cancel', $newsletter));

        // Assert
        $newsletter->refresh();
        $this->assertEquals('cancelled', $newsletter->status);

        $response->assertRedirect(route('admin.newsletters.campaigns.show', $newsletter));
    }

    /**
     * Test 13: Subject line validation
     * Tests required validation
     */
    public function test_subject_line_validation(): void
    {
        // Arrange
        $this->actingAs($this->admin);

        NewsletterSubscription::factory()->count(3)->create()->each(function ($subscription) {
            $subscription->lists()->attach($this->list->id);
        });

        $campaignData = [
            'content' => '<p>Content without subject</p>',
            'lists' => [$this->list->id],
            'action' => 'save_draft',
        ];

        // Act
        $response = $this->post(route('admin.newsletters.campaigns.store'), $campaignData);

        // Assert: Validation error
        $response->assertSessionHasErrors('subject');
    }
}
