<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AccountClaimController extends Controller
{
    /**
     * Show the account claim form
     */
    public function show(Request $request, Customer $customer)
    {
        // Check if customer already has a password
        if ($customer->password !== null) {
            return redirect()->route('login')
                ->with('info', 'This account has already been claimed. Please log in with your existing password.');
        }

        return view('account.claim', compact('customer'));
    }

    /**
     * Process the account claim
     */
    public function store(Request $request, Customer $customer)
    {
        // Check if customer already has a password
        if ($customer->password !== null) {
            return redirect()->route('login')
                ->with('info', 'This account has already been claimed. Please log in with your existing password.');
        }

        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // Set the password
        $customer->password = Hash::make($validated['password']);
        $customer->save();

        // Automatically log the user in
        Auth::login($customer);

        return redirect()->route('dashboard')
            ->with('success', 'Your account has been activated! Welcome to ' . config('app.name') . '.');
    }
}
