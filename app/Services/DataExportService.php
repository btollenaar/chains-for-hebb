<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\NewsletterSubscription;
use ZipArchive;

class DataExportService
{
    public function generateExport(Customer $customer): string
    {
        $tempDir = storage_path('app/temp/exports/' . $customer->id);
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Profile
        file_put_contents("$tempDir/profile.json", json_encode([
            'name' => $customer->name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'role' => $customer->role,
            'created_at' => $customer->created_at?->toISOString(),
        ], JSON_PRETTY_PRINT));

        // Orders
        $orders = $customer->orders()->with('items.item')->get()->map(function ($order) {
            return [
                'id' => $order->id,
                'total' => $order->total,
                'subtotal' => $order->subtotal,
                'tax' => $order->tax,
                'payment_status' => $order->payment_status,
                'payment_method' => $order->payment_method,
                'shipping_address' => $order->shipping_address,
                'billing_address' => $order->billing_address,
                'created_at' => $order->created_at?->toISOString(),
                'items' => $order->items->map(function ($item) {
                    return [
                        'name' => $item->snapshot['name'] ?? 'N/A',
                        'quantity' => $item->quantity,
                        'price' => $item->snapshot['price'] ?? 0,
                    ];
                }),
            ];
        });
        file_put_contents("$tempDir/orders.json", json_encode($orders, JSON_PRETTY_PRINT));

        // Reviews
        $reviews = $customer->reviews()->get()->map(function ($review) {
            return [
                'rating' => $review->rating,
                'title' => $review->title,
                'comment' => $review->comment,
                'status' => $review->status,
                'created_at' => $review->created_at?->toISOString(),
            ];
        });
        file_put_contents("$tempDir/reviews.json", json_encode($reviews, JSON_PRETTY_PRINT));

        // Wishlist
        $wishlist = \App\Models\Wishlist::where('customer_id', $customer->id)
            ->with('item')
            ->get()
            ->map(function ($w) {
                return [
                    'type' => class_basename($w->item_type),
                    'name' => $w->item?->name ?? 'N/A',
                    'added_at' => $w->created_at?->toISOString(),
                ];
            });
        file_put_contents("$tempDir/wishlist.json", json_encode($wishlist, JSON_PRETTY_PRINT));

        // Addresses
        $addresses = \App\Models\Address::where('customer_id', $customer->id)->get()->map(function ($address) {
            return [
                'label' => $address->label,
                'type' => $address->type,
                'street' => $address->street,
                'city' => $address->city,
                'state' => $address->state,
                'zip' => $address->zip,
                'country' => $address->country,
                'is_default' => $address->is_default,
            ];
        });
        file_put_contents("$tempDir/addresses.json", json_encode($addresses, JSON_PRETTY_PRINT));

        // Newsletter subscriptions
        $newsletters = NewsletterSubscription::where('email', $customer->email)->get()->map(function ($sub) {
            return [
                'email' => $sub->email,
                'is_active' => $sub->is_active,
                'source' => $sub->source,
                'subscribed_at' => $sub->created_at?->toISOString(),
                'unsubscribed_at' => $sub->unsubscribed_at,
            ];
        });
        file_put_contents("$tempDir/newsletter_subscriptions.json", json_encode($newsletters, JSON_PRETTY_PRINT));

        // Create ZIP
        $zipPath = "data-exports/{$customer->id}/export-" . now()->format('Y-m-d-His') . ".zip";
        $fullZipPath = storage_path('app/' . $zipPath);
        $zipDir = dirname($fullZipPath);
        if (!is_dir($zipDir)) {
            mkdir($zipDir, 0755, true);
        }

        $zip = new ZipArchive();
        $zip->open($fullZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach (glob("$tempDir/*.json") as $file) {
            $zip->addFile($file, basename($file));
        }
        $zip->close();

        // Cleanup temp files
        array_map('unlink', glob("$tempDir/*.json"));
        rmdir($tempDir);

        return $zipPath;
    }
}
