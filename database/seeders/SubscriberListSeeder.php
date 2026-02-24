<?php

namespace Database\Seeders;

use App\Models\NewsletterSubscription;
use App\Models\SubscriberList;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriberListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allSubscribers = SubscriberList::create([
            'name' => 'All Subscribers',
            'description' => 'All active newsletter subscribers',
            'is_default' => true,
            'is_system' => true,
            'subscriber_count' => 0,
        ]);

        $customers = SubscriberList::create([
            'name' => 'Customers',
            'description' => 'Subscribers who have made a purchase',
            'is_default' => false,
            'is_system' => true,
            'subscriber_count' => 0,
        ]);

        $existingSubscribers = NewsletterSubscription::active()->get();

        foreach ($existingSubscribers as $subscriber) {
            $subscriber->lists()->attach($allSubscribers->id);
        }

        $allSubscribers->updateSubscriberCount();

        $this->command->info('Created 2 system subscriber lists');
        $this->command->info('Assigned ' . $existingSubscribers->count() . ' existing subscribers to "All Subscribers" list');
    }
}
