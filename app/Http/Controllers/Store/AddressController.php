<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = Auth::user()->addresses()->latest()->get();
        return view('addresses.index', compact('addresses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:100',
            'type' => 'required|in:shipping,billing,both',
            'is_default' => 'boolean',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => ['required', 'string', 'size:2', 'regex:/^[A-Z]{2}$/'],
            'zip' => ['required', 'string', 'max:10', 'regex:/^\d{5}(-\d{4})?$/'],
            'country' => 'string|size:2',
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[\d\s\-\(\)\+\.]+$/'],
        ]);

        $validated['customer_id'] = Auth::id();
        $validated['is_default'] = $request->boolean('is_default');

        // First address is always default
        if (Auth::user()->addresses()->count() === 0) {
            $validated['is_default'] = true;
        }

        Address::create($validated);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Address saved successfully.']);
        }

        return redirect()->route('addresses.index')->with('success', 'Address saved successfully.');
    }

    public function update(Request $request, Address $address)
    {
        $this->authorize('update', $address);

        $validated = $request->validate([
            'label' => 'required|string|max:100',
            'type' => 'required|in:shipping,billing,both',
            'is_default' => 'boolean',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => ['required', 'string', 'size:2', 'regex:/^[A-Z]{2}$/'],
            'zip' => ['required', 'string', 'max:10', 'regex:/^\d{5}(-\d{4})?$/'],
            'country' => 'string|size:2',
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[\d\s\-\(\)\+\.]+$/'],
        ]);

        $validated['is_default'] = $request->boolean('is_default');
        $address->update($validated);

        return redirect()->route('addresses.index')->with('success', 'Address updated successfully.');
    }

    public function destroy(Address $address)
    {
        $this->authorize('delete', $address);

        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $newDefault = Auth::user()->addresses()->first();
            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        return redirect()->route('addresses.index')->with('success', 'Address deleted successfully.');
    }

    public function setDefault(Address $address)
    {
        $this->authorize('update', $address);
        $address->update(['is_default' => true]);

        return redirect()->route('addresses.index')->with('success', 'Default address updated.');
    }

    public function jsonIndex()
    {
        $addresses = Auth::user()->addresses()->get();
        return response()->json($addresses);
    }
}
