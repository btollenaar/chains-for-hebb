<?php

namespace Database\Seeders;

use App\Models\DonationTier;
use Illuminate\Database\Seeder;

class DonationTierSeeder extends Seeder
{
    public function run(): void
    {
        $tiers = [
            [
                'name' => 'Ace Maker',
                'slug' => 'ace-maker',
                'suggested_amount' => 100.00,
                'description' => 'Name on permanent donor plaque at the course',
                'perks' => "Name on permanent donor plaque\nExclusive Ace Maker sticker\nDonor wall recognition",
                'badge_icon' => '🏆',
                'badge_color' => '#8B6914',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Eagle',
                'slug' => 'eagle',
                'suggested_amount' => 50.00,
                'description' => 'Sticker pack + donor wall recognition',
                'perks' => "Chains for Hebb sticker\nDonor wall recognition",
                'badge_icon' => '🦅',
                'badge_color' => '#2D5016',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Birdie',
                'slug' => 'birdie',
                'suggested_amount' => 25.00,
                'description' => 'Donor wall recognition',
                'perks' => "Donor wall recognition\nThank you email",
                'badge_icon' => '🐦',
                'badge_color' => '#E85D04',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Par',
                'slug' => 'par',
                'suggested_amount' => 10.00,
                'description' => 'Thank you email with project updates',
                'perks' => "Thank you email\nProject update emails",
                'badge_icon' => '⛳',
                'badge_color' => '#2D8B46',
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($tiers as $tier) {
            DonationTier::create($tier);
        }
    }
}
