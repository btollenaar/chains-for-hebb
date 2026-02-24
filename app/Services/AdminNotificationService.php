<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\Review;
use App\Notifications\NewOrderNotification;
use App\Notifications\NewReturnRequestNotification;
use App\Notifications\NewReviewNotification;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

class AdminNotificationService
{
    /**
     * Get all admin users to receive notifications
     */
    protected static function getAdmins(): Collection
    {
        return Customer::where('is_admin', true)->get();
    }

    public static function notifyNewOrder(Order $order): void
    {
        Notification::send(static::getAdmins(), new NewOrderNotification($order));
    }

    public static function notifyNewReview(Review $review): void
    {
        Notification::send(static::getAdmins(), new NewReviewNotification($review));
    }

    public static function notifyNewReturnRequest(ReturnRequest $returnRequest): void
    {
        Notification::send(static::getAdmins(), new NewReturnRequestNotification($returnRequest));
    }

    public static function notifyLowStock(Collection $products): void
    {
        Notification::send(static::getAdmins(), new LowStockNotification($products));
    }
}
