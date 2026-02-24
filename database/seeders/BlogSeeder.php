<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        // Get first customer (admin)
        $admin = Customer::first();

        if (!$admin) {
            $this->command->error('No customers found. Please create a customer first.');
            return;
        }

        // Create blog categories
        $categories = [
            [
                'name' => 'Product Updates',
                'slug' => 'product-updates',
                'description' => 'New products, design drops, and collection announcements.'
            ],
            [
                'name' => 'Behind the Scenes',
                'slug' => 'behind-the-scenes',
                'description' => 'How our products are designed, printed, and shipped.'
            ],
            [
                'name' => 'Style Guides',
                'slug' => 'style-guides',
                'description' => 'Tips on styling, gifting, and getting the most out of your purchases.'
            ],
        ];

        foreach ($categories as $category) {
            BlogCategory::create($category);
        }

        // Create sample blog posts
        $productUpdates = BlogCategory::where('slug', 'product-updates')->first();
        $behindTheScenes = BlogCategory::where('slug', 'behind-the-scenes')->first();
        $styleGuides = BlogCategory::where('slug', 'style-guides')->first();

        $posts = [
            [
                'category_id' => $productUpdates->id,
                'author_id' => $admin->id,
                'title' => 'New spring collection just dropped',
                'slug' => 'new-spring-collection',
                'excerpt' => 'Fresh designs, updated colorways, and new product types now available in the shop.',
                'content' => '<p>We\'ve been working on something special — our latest collection features bold new designs across t-shirts, hoodies, mugs, and more.</p>

<h2>What\'s New</h2>
<ul>
<li>15 new original designs across multiple product types</li>
<li>Expanded colorway options on our bestselling items</li>
<li>New product category: all-over print items</li>
</ul>

<p>All new products are available now. Check them out in the shop!</p>',
                'published' => true,
                'published_at' => now()->subDays(30),
            ],
            [
                'category_id' => $behindTheScenes->id,
                'author_id' => $admin->id,
                'title' => 'From design to your doorstep: how your order is made',
                'slug' => 'how-your-order-is-made',
                'excerpt' => 'A look at the print-on-demand process behind every product we ship.',
                'content' => '<p>Every order starts fresh — printed just for you. Here\'s a peek at how it all works behind the scenes.</p>
<h2>The process</h2>
<ul>
<li>Design: our team creates original artwork and prepares print-ready files</li>
<li>Print: your order is printed on premium materials by our fulfillment partner</li>
<li>Quality check: each item is inspected before packing</li>
<li>Ship: packed carefully and shipped directly to your door</li>
<li>Track: you get tracking info so you always know where your order is</li>
</ul>
<p>This print-on-demand approach means less waste and more care in every product.</p>',
                'published' => true,
                'published_at' => now()->subDays(20),
            ],
            [
                'category_id' => $styleGuides->id,
                'author_id' => $admin->id,
                'title' => 'The perfect gift guide for any occasion',
                'slug' => 'gift-guide-any-occasion',
                'excerpt' => 'Not sure what to get? Here are our top picks for birthdays, holidays, and just because.',
                'content' => '<p>Our products make great gifts — unique designs that you won\'t find in stores. Here are our recommendations by occasion.</p>
<h2>Top picks</h2>
<ul>
<li>Birthdays: custom mugs and graphic tees are always a hit</li>
<li>Holidays: cozy hoodies and accessories make thoughtful gifts</li>
<li>Just because: phone cases and stickers are fun, affordable surprises</li>
<li>Housewarming: posters and art prints add personality to any space</li>
</ul>
<p>Browse the full collection to find something perfect for someone special.</p>',
                'published' => true,
                'published_at' => now()->subDays(5),
            ],
        ];

        foreach ($posts as $post) {
            BlogPost::create($post);
        }

        $this->command->info('Blog categories and posts seeded successfully!');
    }
}
