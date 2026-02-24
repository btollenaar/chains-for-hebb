<?php

namespace App\Notifications;

use App\Models\ReturnRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewReturnRequestNotification extends Notification
{
    use Queueable;

    public function __construct(public ReturnRequest $returnRequest)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_return_request',
            'title' => 'New Return Request',
            'message' => 'Return #' . $this->returnRequest->return_number
                . ' for order #' . ($this->returnRequest->order->order_number ?? 'N/A')
                . ' - ' . ($this->returnRequest->customer->name ?? 'Unknown'),
            'return_id' => $this->returnRequest->id,
            'return_number' => $this->returnRequest->return_number,
            'url' => route('admin.returns.show', $this->returnRequest),
            'icon' => 'fas fa-undo',
            'color' => 'yellow',
        ];
    }
}
