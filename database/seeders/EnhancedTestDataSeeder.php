<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\Customer;
use App\Models\Newsletter;
use App\Models\NewsletterSubscription;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\SubscriberList;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class EnhancedTestDataSeeder extends Seeder
{
    /**
     * Run comprehensive test data seeding for development/testing.
     *
     * Creates:
     * - Newsletter subscriptions
     * - Newsletter campaigns (draft, sent, scheduled)
     * - Additional orders with various statuses
     * - Product reviews
     * - Cart items for testing
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding enhanced test data...');

        $this->seedNewsletterSubscriptions();
        $this->seedNewsletters();
        $this->seedAdditionalOrders();
        $this->seedReviews();
        $this->seedCartItems();

        $this->command->info('✅ Enhanced test data seeded successfully!');
    }

    /**
     * Seed newsletter subscriptions for existing customers
     */
    private function seedNewsletterSubscriptions(): void
    {
        $this->command->info('  📧 Creating newsletter subscriptions...');

        $customers = Customer::where('role', 'customer')->get();

        // Subscribe 70% of customers to newsletter
        $subscribersCount = (int) ceil($customers->count() * 0.7);
        $subscribedCustomers = $customers->random(min($subscribersCount, $customers->count()));

        foreach ($subscribedCustomers as $customer) {
            NewsletterSubscription::updateOrCreate(
                ['email' => $customer->email],
                [
                    'name' => $customer->name,
                    'source' => Arr::random(['signup_form', 'checkout', 'manual']),
                    'is_active' => true,
                    'subscribed_at' => Carbon::now()->subDays(rand(1, 90)),
                ]
            );
        }

        $this->command->info("    ✓ Created {$subscribersCount} newsletter subscriptions");
    }

    /**
     * Seed newsletter campaigns with various statuses
     */
    private function seedNewsletters(): void
    {
        $this->command->info('  📨 Creating newsletter campaigns...');

        // Get or create the "All Subscribers" system list
        $allSubscribers = SubscriberList::firstOrCreate(
            [
                'name' => 'All Subscribers',
                'is_system' => true,
            ],
            [
                'description' => 'All active newsletter subscribers',
                'is_default' => true,
            ]
        );

        $campaigns = [
            [
                'subject' => 'Welcome to Our Newsletter! 🎉',
                'preview_text' => 'Thank you for subscribing. Here\'s what you can expect...',
                'content' => '<h2>Welcome!</h2><p>Thank you for joining our newsletter. We\'ll keep you updated with the latest products and special offers.</p><p><strong>What to expect:</strong></p><ul><li>Weekly product highlights</li><li>Exclusive subscriber discounts</li><li>New design announcements</li></ul>',
                'status' => 'sent',
                'scheduled_at' => Carbon::now()->subDays(30),
                'sent_at' => Carbon::now()->subDays(30),
            ],
            [
                'subject' => 'New Products Just Arrived! 🆕',
                'preview_text' => 'Check out our latest arrivals and special promotions',
                'content' => '<h2>New Arrivals</h2><p>We\'re excited to announce our latest product lineup!</p><p>Shop now and get <strong>15% off</strong> your first purchase with code: WELCOME15</p>',
                'status' => 'sent',
                'scheduled_at' => Carbon::now()->subDays(15),
                'sent_at' => Carbon::now()->subDays(15),
            ],
            [
                'subject' => 'Holiday Special - Limited Time Offer! 🎁',
                'preview_text' => 'Exclusive holiday discounts for our valued subscribers',
                'content' => '<h2>Holiday Specials</h2><p>Celebrate the season with our exclusive offers!</p><ul><li>Buy 2, Get 1 Free on select items</li><li>Free shipping on orders over $50</li><li>20% off all prints</li></ul><p><a href="#">Shop Now</a></p>',
                'status' => 'sent',
                'scheduled_at' => Carbon::now()->subDays(7),
                'sent_at' => Carbon::now()->subDays(7),
            ],
            [
                'subject' => 'Year-End Favorites & Product Roundup',
                'preview_text' => 'Our most popular products and best-selling designs',
                'content' => '<h2>Year-End Roundup</h2><p>As we close out the year, here are our customers\' favorite products and best-selling designs!</p>',
                'status' => 'scheduled',
                'scheduled_at' => Carbon::now()->addDays(3),
                'sent_at' => null,
            ],
            [
                'subject' => '[DRAFT] New Product Announcement',
                'preview_text' => 'Introducing our newest print collection',
                'content' => '<h2>Coming Soon</h2><p>We\'re working on something exciting...</p>',
                'status' => 'draft',
                'scheduled_at' => null,
                'sent_at' => null,
            ],
        ];

        foreach ($campaigns as $campaignData) {
            // Add required fields
            $campaignData['from_name'] = 'PrintStore';
            $campaignData['from_email'] = 'newsletter@printstore.example.com';
            $campaignData['recipient_count'] = 0;
            $campaignData['sent_count'] = 0;
            $campaignData['failed_count'] = 0;
            $campaignData['open_count'] = 0;
            $campaignData['click_count'] = 0;

            $campaign = Newsletter::create($campaignData);

            // Attach to all subscribers list
            $campaign->lists()->attach($allSubscribers->id);

            // For sent campaigns, create some send history
            if ($campaign->status === 'sent') {
                $subscriberCount = rand(8, 15);
                $subscribers = NewsletterSubscription::active()->inRandomOrder()->limit($subscriberCount)->get();

                $openCount = 0;
                $clickCount = 0;

                foreach ($subscribers as $subscriber) {
                    $opened = rand(0, 1);
                    $clicked = $opened && rand(0, 1); // Can only click if opened

                    if ($opened) $openCount++;
                    if ($clicked) $clickCount++;

                    $campaign->sends()->create([
                        'newsletter_subscription_id' => $subscriber->id,
                        'sent_at' => $campaign->sent_at,
                        'opened_at' => $opened ? Carbon::parse($campaign->sent_at)->addHours(rand(1, 48)) : null,
                        'clicked_at' => $clicked ? Carbon::parse($campaign->sent_at)->addHours(rand(2, 72)) : null,
                    ]);
                }

                // Update campaign stats
                $campaign->update([
                    'recipient_count' => $subscriberCount,
                    'sent_count' => $subscriberCount,
                    'open_count' => $openCount,
                    'click_count' => $clickCount,
                ]);
            }
        }

        $this->command->info('    ✓ Created ' . count($campaigns) . ' newsletter campaigns');
    }

    /**
     * Seed additional orders with various statuses
     */
    private function seedAdditionalOrders(): void
    {
        $this->command->info('  🛒 Creating additional orders...');

        $customers = Customer::where('role', 'customer')->get();
        $products = Product::where('status', 'active')->get();
        $taxRate = config('business.payments.tax_rate', 0.07);

        if ($customers->isEmpty()) {
            $this->command->warn('    ⚠ Skipping orders: no customers found');
            return;
        }

        $orderStatuses = [
            ['payment_status' => 'paid', 'fulfillment_status' => 'completed'],
            ['payment_status' => 'paid', 'fulfillment_status' => 'processing'],
            ['payment_status' => 'paid', 'fulfillment_status' => 'pending'],
            ['payment_status' => 'pending', 'fulfillment_status' => 'pending'],
            ['payment_status' => 'failed', 'fulfillment_status' => 'cancelled'],
        ];

        $createdOrders = 0;

        // Create 15-20 additional orders
        for ($i = 0; $i < rand(15, 20); $i++) {
            $customer = $customers->random();
            $status = Arr::random($orderStatuses);

            // Randomly select 1-4 items
            $itemCount = rand(1, 4);
            $items = [];

            for ($j = 0; $j < $itemCount; $j++) {
                if ($products->isNotEmpty()) {
                    $items[] = ['item' => $products->random(), 'quantity' => rand(1, 3)];
                }
            }

            if (empty($items)) {
                continue;
            }

            // Calculate totals
            $subtotal = 0;
            foreach ($items as $entry) {
                $price = $entry['item']->current_price ?? $entry['item']->price;
                $subtotal += $price * $entry['quantity'];
            }

            $taxAmount = round($subtotal * $taxRate, 2);
            $discountAmount = rand(0, 1) ? round($subtotal * 0.1, 2) : 0; // 10% discount sometimes
            $total = $subtotal + $taxAmount - $discountAmount;

            $address = [
                'street' => $customer->billing_street ?? '123 Main St',
                'city' => $customer->billing_city ?? 'Clackamas',
                'state' => $customer->billing_state ?? 'OR',
                'zip' => $customer->billing_zip ?? '97015',
                'country' => $customer->billing_country ?? 'US',
            ];

            // Create order with realistic timestamp
            $createdAt = Carbon::now()->subDays(rand(1, 90));

            $order = Order::create([
                'customer_id' => $customer->id,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $total,
                'payment_method' => Arr::random(['stripe', 'cash', 'check']),
                'payment_status' => $status['payment_status'],
                'fulfillment_status' => $status['fulfillment_status'],
                'billing_address' => $address,
                'shipping_address' => $address,
                'notes' => rand(0, 1) ? 'Please deliver before 5pm' : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // Create order items
            foreach ($items as $entry) {
                $orderItem = new OrderItem([
                    'order_id' => $order->id,
                    'item_type' => get_class($entry['item']),
                    'item_id' => $entry['item']->id,
                    'quantity' => $entry['quantity'],
                ]);

                $orderItem->setRelation('item', $entry['item']);
                $orderItem->snapshotItemDetails();
                $orderItem->save();
            }

            $order->calculateTotals();
            $createdOrders++;
        }

        $this->command->info("    ✓ Created {$createdOrders} additional orders");
    }

    /**
     * Seed reviews for products
     */
    private function seedReviews(): void
    {
        $this->command->info('  ⭐ Creating reviews...');

        $customers = Customer::where('role', 'customer')->get();
        $products = Product::where('status', 'active')->get();

        if ($customers->isEmpty()) {
            $this->command->warn('    ⚠ Skipping reviews: no customers found');
            return;
        }

        $createdReviews = 0;

        // Create reviews for products
        if ($products->isNotEmpty()) {
            foreach ($products->random(min(5, $products->count())) as $product) {
                $reviewCount = rand(2, 5);

                for ($i = 0; $i < $reviewCount; $i++) {
                    $customer = $customers->random();
                    $rating = Arr::random([3, 4, 4, 5, 5, 5]); // Bias towards positive reviews

                    // Check if customer has ordered this product
                    $verifiedPurchase = Order::where('customer_id', $customer->id)
                        ->whereHas('items', function ($query) use ($product) {
                            $query->where('item_type', Product::class)
                                  ->where('item_id', $product->id);
                        })
                        ->where('payment_status', 'paid')
                        ->exists();

                    Review::create([
                        'customer_id' => $customer->id,
                        'reviewable_type' => Product::class,
                        'reviewable_id' => $product->id,
                        'rating' => $rating,
                        'title' => $this->getReviewTitle($rating),
                        'comment' => $this->getReviewComment($rating),
                        'verified_purchase' => $verifiedPurchase,
                        'status' => Arr::random(['approved', 'approved', 'approved', 'pending']),
                        'helpful_count' => rand(0, 10),
                        'not_helpful_count' => rand(0, 3),
                        'created_at' => Carbon::now()->subDays(rand(1, 60)),
                    ]);

                    $createdReviews++;
                }
            }
        }

        $this->command->info("    ✓ Created {$createdReviews} reviews");
    }

    /**
     * Seed cart items for testing cart functionality
     */
    private function seedCartItems(): void
    {
        $this->command->info('  🛒 Creating cart items...');

        $customers = Customer::where('role', 'customer')->inRandomOrder()->limit(5)->get();
        $products = Product::where('status', 'active')->get();

        if ($customers->isEmpty()) {
            $this->command->warn('    ⚠ Skipping cart items: no customers found');
            return;
        }

        $createdItems = 0;

        foreach ($customers as $customer) {
            $itemCount = rand(1, 4);

            for ($i = 0; $i < $itemCount; $i++) {
                if ($products->isNotEmpty()) {
                    Cart::create([
                        'customer_id' => $customer->id,
                        'item_type' => Product::class,
                        'item_id' => $products->random()->id,
                        'quantity' => rand(1, 3),
                    ]);
                    $createdItems++;
                }
            }
        }

        $this->command->info("    ✓ Created {$createdItems} cart items for " . $customers->count() . " customers");
    }

    /**
     * Generate review title based on rating
     */
    private function getReviewTitle(int $rating): string
    {
        $titles = [
            5 => ['Excellent product!', 'Love it!', 'Highly recommend', 'Perfect!', 'Amazing quality'],
            4 => ['Great product', 'Very good', 'Happy with purchase', 'Good quality', 'Satisfied'],
            3 => ['It\'s okay', 'Average', 'Decent product', 'Not bad', 'Acceptable'],
            2 => ['Disappointed', 'Not great', 'Expected better', 'Below average', 'Not satisfied'],
            1 => ['Very disappointed', 'Poor quality', 'Would not recommend', 'Terrible', 'Waste of money'],
        ];

        return Arr::random($titles[$rating] ?? $titles[3]);
    }

    /**
     * Generate review comment based on rating
     */
    private function getReviewComment(int $rating): string
    {
        $comments = [
            5 => [
                'This product exceeded my expectations! The quality is outstanding and it works exactly as described.',
                'Absolutely love this! It\'s become an essential part of my daily routine.',
                'Best purchase I\'ve made in a long time. Highly recommend to anyone looking for quality.',
                'Fantastic product! Fast shipping and excellent customer service too.',
            ],
            4 => [
                'Very good product overall. Does what it\'s supposed to do and good value for money.',
                'Happy with this purchase. Quality is good, would buy again.',
                'Great product! Only minor issue is the packaging could be better.',
                'Good quality and works well. A bit pricey but worth it.',
            ],
            3 => [
                'It\'s okay. Does the job but nothing special.',
                'Average product. Not bad but not amazing either.',
                'Decent for the price. Had some minor issues but overall acceptable.',
                'It works, but I expected a bit more based on the description.',
            ],
        ];

        return Arr::random($comments[$rating] ?? $comments[3]);
    }
}
