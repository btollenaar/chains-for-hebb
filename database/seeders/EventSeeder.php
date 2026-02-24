<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            [
                'title' => 'Trail Clearing Work Party',
                'slug' => 'trail-clearing-work-party',
                'description' => 'Help us clear the first 9 fairways! We\'ll be removing brush, clearing sight lines, and preparing the ground for tee pads.',
                'content' => '<p>Join us for our first official work party at Hebb Park! We\'ll be clearing brush and small trees to create fairways for the front 9 holes.</p><p>All skill levels welcome. We\'ll provide tools and guidance — just bring yourself, water, and a willingness to work.</p>',
                'event_type' => 'work_party',
                'location_name' => 'Hebb County Park, West Linn, OR',
                'starts_at' => now()->addWeeks(3)->setHour(9)->setMinute(0),
                'ends_at' => now()->addWeeks(3)->setHour(14)->setMinute(0),
                'max_attendees' => 30,
                'what_to_bring' => 'Work gloves, sturdy shoes/boots, water bottle, sunscreen. We\'ll provide loppers, rakes, and saws.',
                'is_published' => true,
                'is_featured' => true,
            ],
            [
                'title' => 'Fundraiser Disc Golf Tournament',
                'slug' => 'fundraiser-disc-golf-tournament',
                'description' => 'A fun, casual tournament to raise money for the course. Play 18 holes at nearby Pier Park and support Chains for Hebb.',
                'content' => '<p>Join us for a fun, casual disc golf tournament! All entry fees go directly to the Chains for Hebb fund.</p><p>Format: Doubles, best-shot. All skill levels welcome. Prizes for top teams and longest drive.</p>',
                'event_type' => 'fundraiser',
                'location_name' => 'Pier Park DGC, Portland, OR',
                'starts_at' => now()->addWeeks(6)->setHour(10)->setMinute(0),
                'ends_at' => now()->addWeeks(6)->setHour(15)->setMinute(0),
                'max_attendees' => 60,
                'what_to_bring' => 'Your discs, a good attitude, and friends!',
                'is_published' => true,
                'is_featured' => true,
            ],
            [
                'title' => 'Community Meetup & Disc Golf Demo',
                'slug' => 'community-meetup-disc-golf-demo',
                'description' => 'Come learn about disc golf! We\'ll have discs to try, course design previews, and info about how to get involved.',
                'content' => '<p>Never played disc golf? Curious about the Chains for Hebb project? Join us for a casual community meetup at Hebb Park.</p><p>We\'ll have loaner discs for you to try, show off the course design plans, and answer all your questions.</p>',
                'event_type' => 'meetup',
                'location_name' => 'Hebb County Park, West Linn, OR',
                'starts_at' => now()->addWeeks(2)->setHour(11)->setMinute(0),
                'ends_at' => now()->addWeeks(2)->setHour(14)->setMinute(0),
                'max_attendees' => null,
                'what_to_bring' => 'Comfortable shoes and curiosity! Discs provided.',
                'is_published' => true,
                'is_featured' => true,
            ],
        ];

        foreach ($events as $event) {
            Event::create($event);
        }
    }
}
