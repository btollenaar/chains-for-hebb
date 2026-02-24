<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Core Data
            CustomerSeeder::class,
            DummyUsersSeeder::class,
            SettingsSeeder::class,

            // Product Categories & Data
            ComprehensiveProductCategorySeeder::class,
            Comprehensive100ProductSeeder::class,

            // Content
            BlogSeeder::class,
            AboutSeeder::class,

            // Orders
            OrderSeeder::class,

            // Newsletter System
            SubscriberListSeeder::class,

            // Coupons
            CouponSeeder::class,

            // Customer Tags
            TagSeeder::class,

            // Enhanced Test Data (orders, reviews, cart items, newsletters)
            EnhancedTestDataSeeder::class,

            // Chains for Hebb: Fundraiser-specific data
            DonationTierSeeder::class,
            CmsPageSeeder::class,
            EventSeeder::class,
            SponsorTierSeeder::class,
            SponsorSeeder::class,
            FundraisingMilestoneSeeder::class,
            FundraisingBreakdownSeeder::class,
            GallerySeeder::class,
        ]);
    }
}
