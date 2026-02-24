<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = Customer::take(4)->get();
        $products = Product::take(5)->get();
        $taxRate = config('business.payments.tax_rate', 0.0);

        if ($customers->isEmpty() || $products->isEmpty()) {
            $this->command->warn('Skipping OrderSeeder: customers or products missing.');
            return;
        }

        foreach ($customers as $customer) {
            $items = [];

            // Add 1-3 random products per order
            $productCount = rand(1, 3);
            for ($i = 0; $i < $productCount && $i < $products->count(); $i++) {
                $items[] = ['item' => $products->random(), 'quantity' => rand(1, 2)];
            }

            $subtotal = 0;
            foreach ($items as $entry) {
                $price = $entry['item']->current_price ?? $entry['item']->price;
                $subtotal += $price * $entry['quantity'];
            }

            $taxAmount = round($subtotal * $taxRate, 2);
            $total = $subtotal + $taxAmount;

            $address = [
                'street' => $customer->billing_street ?? '123 Main St',
                'city' => $customer->billing_city ?? 'Clackamas',
                'state' => $customer->billing_state ?? 'OR',
                'zip' => $customer->billing_zip ?? '97015',
                'country' => $customer->billing_country ?? 'US',
            ];

            $order = Order::create([
                'customer_id' => $customer->id,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => 0,
                'total_amount' => $total,
                'payment_method' => 'stripe',
                'payment_status' => 'paid',
                'fulfillment_status' => Arr::random(['completed', 'processing']),
                'billing_address' => $address,
                'shipping_address' => $address,
                'notes' => null,
            ]);

            foreach ($items as $entry) {
                $orderItem = new OrderItem([
                    'order_id' => $order->id,
                    'item_type' => get_class($entry['item']),
                    'item_id' => $entry['item']->id,
                    'quantity' => $entry['quantity'],
                ]);

                // Set the item relation so snapshotItemDetails can access it
                $orderItem->setRelation('item', $entry['item']);
                $orderItem->snapshotItemDetails();
                $orderItem->save();
            }

            $order->calculateTotals();
        }
    }
}
