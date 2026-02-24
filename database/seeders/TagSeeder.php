<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            [
                'name' => 'VIP',
                'slug' => 'vip',
                'color' => '#F59E0B',
                'description' => 'High-value customers who deserve premium treatment and exclusive offers.',
            ],
            [
                'name' => 'Wholesale',
                'slug' => 'wholesale',
                'color' => '#2563EB',
                'description' => 'Customers who purchase in bulk or at wholesale pricing.',
            ],
            [
                'name' => 'Brand Fan',
                'slug' => 'brand-fan',
                'color' => '#10B981',
                'description' => 'Customers who actively engage with our brand and share our products.',
            ],
            [
                'name' => 'Repeat Customer',
                'slug' => 'repeat-customer',
                'color' => '#7C3AED',
                'description' => 'Loyal customers who have made multiple purchases.',
            ],
            [
                'name' => 'Newsletter VIP',
                'slug' => 'newsletter-vip',
                'color' => '#EC4899',
                'description' => 'Highly engaged newsletter subscribers with strong open and click rates.',
            ],
        ];

        foreach ($tags as $tag) {
            Tag::updateOrCreate(
                ['slug' => $tag['slug']],
                $tag
            );
        }
    }
}
