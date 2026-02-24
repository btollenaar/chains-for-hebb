<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user - Harry Henderson
        $admin = new Customer();
        $admin->name = 'Harry Henderson';
        $admin->email = 'harry.admin@printstore.com';
        $admin->password = bcrypt('password');
        $admin->phone = '503-555-0100';
        $admin->email_verified_at = now();
        $admin->billing_street = '123 Admin Street';
        $admin->billing_city = 'Clackamas';
        $admin->billing_state = 'OR';
        $admin->billing_zip = '97015';
        $admin->billing_country = 'US';
        $admin->role = 'admin';
        $admin->is_admin = true;
        $admin->save();

        // Create test customer accounts
        Customer::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'phone' => '503-555-0101',
            'is_admin' => false,
            'email_verified_at' => now(),
            'billing_street' => '456 Evergreen Ave',
            'billing_city' => 'Oregon City',
            'billing_state' => 'OR',
            'billing_zip' => '97045',
            'billing_country' => 'USA',
            'shipping_street' => '456 Evergreen Ave',
            'shipping_city' => 'Oregon City',
            'shipping_state' => 'OR',
            'shipping_zip' => '97045',
            'shipping_country' => 'USA',
        ]);

        Customer::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => 'password',
            'phone' => '503-555-0102',
            'is_admin' => false,
            'email_verified_at' => now(),
            'billing_street' => '789 Hawthorne Blvd',
            'billing_city' => 'Lake Oswego',
            'billing_state' => 'OR',
            'billing_zip' => '97034',
            'billing_country' => 'USA',
        ]);

        Customer::create([
            'name' => 'Robert Johnson',
            'email' => 'robert@example.com',
            'password' => 'password',
            'phone' => '503-555-0103',
            'is_admin' => false,
            'email_verified_at' => now(),
            'billing_street' => '321 Pioneer Lane',
            'billing_city' => 'Milwaukie',
            'billing_state' => 'OR',
            'billing_zip' => '97222',
            'billing_country' => 'USA',
        ]);

        Customer::create([
            'name' => 'Emily Davis',
            'email' => 'emily@example.com',
            'password' => 'password',
            'phone' => '503-555-0104',
            'is_admin' => false,
            'email_verified_at' => now(),
            'billing_street' => '654 Cedar Street',
            'billing_city' => 'Tigard',
            'billing_state' => 'OR',
            'billing_zip' => '97223',
            'billing_country' => 'USA',
        ]);
    }
}
