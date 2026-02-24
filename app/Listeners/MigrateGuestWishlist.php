<?php

namespace App\Listeners;

use App\Models\Wishlist;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MigrateGuestWishlist
{
    public function handle(Login|Registered $event): void
    {
        $user = $event->user;
        $oldSessionId = session()->get('guest_session_id');

        if (!$oldSessionId) {
            return;
        }

        try {
            DB::beginTransaction();

            $guestItems = Wishlist::where('session_id', $oldSessionId)
                ->whereNull('customer_id')
                ->get();

            foreach ($guestItems as $guestItem) {
                $existing = Wishlist::where('customer_id', $user->id)
                    ->where('item_type', $guestItem->item_type)
                    ->where('item_id', $guestItem->item_id)
                    ->first();

                if ($existing) {
                    $guestItem->delete();
                } else {
                    $guestItem->customer_id = $user->id;
                    $guestItem->session_id = null;
                    $guestItem->save();
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Guest wishlist migration failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'session_id' => $oldSessionId,
            ]);
        }
    }
}
