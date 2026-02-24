<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class DummyUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Note: Primary admin (Harry Henderson) is created by CustomerSeeder

        // Regular Customer Accounts (for testing orders)
        $customers = [
            [
                'name' => 'Lisa Patterson',
                'email' => 'lisa.customer@example.com',
                'phone' => '555-0501',
                'street' => '444 Customer Lane',
                'city' => 'Clackamas',
            ],
            [
                'name' => 'Mark Johnson',
                'email' => 'mark.customer@example.com',
                'phone' => '555-0502',
                'street' => '555 Buyer Street',
                'city' => 'Lake Oswego',
            ],
            [
                'name' => 'Amanda Garcia',
                'email' => 'amanda.customer@example.com',
                'phone' => '555-0503',
                'street' => '666 Client Road',
                'city' => 'Tigard',
            ],
            [
                'name' => 'Chris Taylor',
                'email' => 'chris.customer@example.com',
                'phone' => '555-0504',
                'street' => '777 Service Ave',
                'city' => 'Oregon City',
            ],
            [
                'name' => 'Jennifer Lee',
                'email' => 'jennifer.customer@example.com',
                'phone' => '555-0505',
                'street' => '888 Main Boulevard',
                'city' => 'Milwaukie',
            ],
        ];

        foreach ($customers as $customerData) {
            Customer::create([
                'name' => $customerData['name'],
                'email' => $customerData['email'],
                'password' => 'password',
                'phone' => $customerData['phone'],
                'role' => 'customer',
                'is_admin' => false,
                'email_verified_at' => now(),
                'billing_street' => $customerData['street'],
                'billing_city' => $customerData['city'],
                'billing_state' => 'OR',
                'billing_zip' => '97015',
                'billing_country' => 'USA',
                'shipping_street' => $customerData['street'],
                'shipping_city' => $customerData['city'],
                'shipping_state' => 'OR',
                'shipping_zip' => '97015',
                'shipping_country' => 'USA',
            ]);

            $this->command->info('✓ Created customer: ' . $customerData['email']);
        }

        $this->command->info('');
        $this->command->info('=== SUMMARY ===');
        $this->command->info('Created 5 test customers:');
        $this->command->info('  - 5 Regular Customers');
        $this->command->info('  - 1 Admin (Harry Henderson) created by CustomerSeeder');
        $this->command->info('');
        $this->command->info('All users have password: password');
    }
}
