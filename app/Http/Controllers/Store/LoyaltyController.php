<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyPoint;
use Illuminate\Support\Facades\Auth;

class LoyaltyController extends Controller
{
    public function index()
    {
        $customer = Auth::user();
        $transactions = LoyaltyPoint::forCustomer($customer->id)
            ->latest()
            ->paginate(20);

        $totalEarned = LoyaltyPoint::forCustomer($customer->id)->earned()->sum('points');
        $totalRedeemed = abs(LoyaltyPoint::forCustomer($customer->id)->redeemed()->sum('points'));

        return view('loyalty.index', compact('customer', 'transactions', 'totalEarned', 'totalRedeemed'));
    }
}
