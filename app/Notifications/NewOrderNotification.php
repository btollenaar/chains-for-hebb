<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_order',
            'title' => 'New Order Received',
            'message' => 'Order #' . $this->order->order_number . ' from ' . ($this->order->customer->name ?? 'Unknown') . ' for $' . number_format($this->order->total_amount, 2),
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'amount' => $this->order->total_amount,
            'url' => route('admin.orders.show', $this->order),
            'icon' => 'fas fa-shopping-cart',
            'color' => 'blue',
        ];
    }
}
