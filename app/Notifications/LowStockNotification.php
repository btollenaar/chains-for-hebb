<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class LowStockNotification extends Notification
{
    use Queueable;

    public function __construct(public Collection $products) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $count = $this->products->count();

        return [
            'type' => 'low_stock',
            'title' => 'Low Stock Alert',
            'message' => $count . ' product' . ($count > 1 ? 's are' : ' is') . ' running low on stock',
            'product_count' => $count,
            'products' => $this->products->take(5)->map(fn($p) => [
                'name' => $p->name,
                'stock' => $p->stock_quantity,
            ])->toArray(),
            'url' => route('admin.products.index', ['stock' => 'low']),
            'icon' => 'fas fa-exclamation-triangle',
            'color' => 'red',
        ];
    }
}
