<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewReviewNotification extends Notification
{
    use Queueable;

    public function __construct(public Review $review) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $this->review->load(['customer', 'reviewable']);

        $itemName = $this->review->reviewable->name ?? 'an item';
        $stars = str_repeat('*', $this->review->rating);

        return [
            'type' => 'new_review',
            'title' => 'New Review Submitted',
            'message' => ($this->review->customer->name ?? 'A customer') . ' left a ' . $this->review->rating . '-star review on ' . $itemName,
            'review_id' => $this->review->id,
            'rating' => $this->review->rating,
            'url' => route('admin.reviews.show', $this->review),
            'icon' => 'fas fa-star',
            'color' => 'yellow',
        ];
    }
}
