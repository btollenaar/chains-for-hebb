<?php

namespace Database\Seeders;

use App\Models\SponsorTier;
use Illuminate\Database\Seeder;

class SponsorTierSeeder extends Seeder
{
    public function run(): void
    {
        $tiers = [
            ['name' => 'Hole Sponsor', 'slug' => 'hole-sponsor', 'min_amount' => 500, 'description' => 'Your name/logo on a permanent tee sign', 'perks' => "Name on tee sign\nLogo on website (XL)\nSocial media feature", 'logo_size' => 'xl', 'sort_order' => 1],
            ['name' => 'Gold Sponsor', 'slug' => 'gold-sponsor', 'min_amount' => 250, 'description' => 'Featured on course kiosk and website', 'perks' => "Name on course kiosk\nLogo on website (LG)\nSocial media mention", 'logo_size' => 'lg', 'sort_order' => 2],
            ['name' => 'Silver Sponsor', 'slug' => 'silver-sponsor', 'min_amount' => 100, 'description' => 'Logo on website and donor plaque', 'perks' => "Logo on website (MD)\nName on donor plaque", 'logo_size' => 'md', 'sort_order' => 3],
            ['name' => 'Bronze Sponsor', 'slug' => 'bronze-sponsor', 'min_amount' => 50, 'description' => 'Name on website sponsors page', 'perks' => "Name on website (SM)", 'logo_size' => 'sm', 'sort_order' => 4],
            ['name' => 'Friend', 'slug' => 'friend', 'min_amount' => 25, 'description' => 'Listed on supporters page', 'perks' => "Name listed on supporters page", 'logo_size' => 'sm', 'sort_order' => 5],
        ];

        foreach ($tiers as $tier) {
            SponsorTier::create($tier);
        }
    }
}
