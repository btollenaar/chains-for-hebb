<?php

namespace Database\Seeders;

use App\Models\Sponsor;
use App\Models\SponsorTier;
use Illuminate\Database\Seeder;

class SponsorSeeder extends Seeder
{
    public function run(): void
    {
        $holeSponsor = SponsorTier::where('slug', 'hole-sponsor')->first();
        $goldSponsor = SponsorTier::where('slug', 'gold-sponsor')->first();

        $sponsors = [
            ['sponsor_tier_id' => $holeSponsor?->id, 'name' => 'West Linn Disc Supply', 'website_url' => null, 'sponsorship_amount' => 500, 'sponsorship_date' => now(), 'is_active' => true, 'is_featured' => true, 'sort_order' => 1],
            ['sponsor_tier_id' => $goldSponsor?->id, 'name' => 'Willamette River Coffee', 'website_url' => null, 'sponsorship_amount' => 250, 'sponsorship_date' => now(), 'is_active' => true, 'is_featured' => true, 'sort_order' => 2],
        ];

        foreach ($sponsors as $sponsor) {
            Sponsor::create($sponsor);
        }
    }
}
