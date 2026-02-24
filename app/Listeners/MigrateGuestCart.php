<?php

namespace App\Listeners;

use App\Models\Cart;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MigrateGuestCart
{
    /**
     * Handle the event.
     */
    public function handle(Login|Registered $event): void
    {
        $user = $event->user;

        // Get the old session ID stored before authentication
        $oldSessionId = session()->get('guest_session_id');

        if (!$oldSessionId) {
            // No guest session to migrate
            return;
        }

        try {
            DB::beginTransaction();

            // Get all guest cart items
            $guestCartItems = Cart::where('session_id', $oldSessionId)
                ->whereNull('customer_id')
                ->get();

            foreach ($guestCartItems as $guestItem) {
                // Check if customer already has this item in their cart
                $existingItem = Cart::where('customer_id', $user->id)
                    ->where('item_type', $guestItem->item_type)
                    ->where('item_id', $guestItem->item_id)
                    ->first();

                if ($existingItem) {
                    // Merge quantities
                    $existingItem->quantity += $guestItem->quantity;
                    $existingItem->save();

                    // Delete the guest cart item
                    $guestItem->delete();
                } else {
                    // Transfer the guest cart item to the customer
                    $guestItem->customer_id = $user->id;
                    $guestItem->session_id = null;
                    $guestItem->save();
                }
            }

            DB::commit();

            // Clear the stored guest session ID
            session()->forget('guest_session_id');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Guest cart migration failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'session_id' => $oldSessionId,
            ]);
        }
    }
}
