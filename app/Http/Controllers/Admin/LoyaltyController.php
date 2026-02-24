<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\LoyaltyService;
use Illuminate\Http\Request;

class LoyaltyController extends Controller
{
    public function adjustPoints(Request $request, Customer $customer, LoyaltyService $loyaltyService)
    {
        $validated = $request->validate([
            'points' => 'required|integer',
            'description' => 'required|string|max:255',
        ]);

        $loyaltyService->adjustPoints($customer, $validated['points'], $validated['description']);

        return redirect()->back()->with('success', "Loyalty points adjusted by {$validated['points']} for {$customer->name}.");
    }
}
