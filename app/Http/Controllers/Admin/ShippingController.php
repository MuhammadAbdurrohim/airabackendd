<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingAddress;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function index()
    {
        $shippingAddresses = ShippingAddress::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.shipping.index', compact('shippingAddresses'));
    }

    public function show(ShippingAddress $shippingAddress)
    {
        return view('admin.shipping.show', compact('shippingAddress'));
    }

    public function edit(ShippingAddress $shippingAddress)
    {
        return view('admin.shipping.edit', compact('shippingAddress'));
    }

    public function update(Request $request, ShippingAddress $shippingAddress)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'notes' => 'nullable|string',
        ]);

        $shippingAddress->update($validated);

        return redirect()
            ->route('admin.shipping.index')
            ->with('success', 'Shipping address updated successfully.');
    }

    public function destroy(ShippingAddress $shippingAddress)
    {
        $shippingAddress->delete();

        return redirect()
            ->route('admin.shipping.index')
            ->with('success', 'Shipping address deleted successfully.');
    }
}
