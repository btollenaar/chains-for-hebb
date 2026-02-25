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

        // Create additional admin users for testing
        $admin2 = new Customer();
        $admin2->name = 'Ben Hier';
        $admin2->email = 'ben.hier@chains4hebb.com';
        $admin2->password = bcrypt('password');
        $admin2->phone = '503-555-0110';
        $admin2->email_verified_at = now();
        $admin2->billing_street = '250 Broadway Ave';
        $admin2->billing_city = 'Portland';
        $admin2->billing_state = 'OR';
        $admin2->billing_zip = '97201';
        $admin2->billing_country = 'US';
        $admin2->role = 'admin';
        $admin2->is_admin = true;
        $admin2->save();

        $admin3 = new Customer();
        $admin3->name = 'Mike Crisp';
        $admin3->email = 'mike.crisp@chains4hebb.com';
        $admin3->password = bcrypt('password');
        $admin3->phone = '503-555-0120';
        $admin3->email_verified_at = now();
        $admin3->billing_street = '88 Division Street';
        $admin3->billing_city = 'Portland';
        $admin3->billing_state = 'OR';
        $admin3->billing_zip = '97202';
        $admin3->billing_country = 'US';
        $admin3->role = 'admin';
        $admin3->is_admin = true;
        $admin3->save();

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
