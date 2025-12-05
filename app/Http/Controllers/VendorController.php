<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->paginate(15);

        return view('vendors.index', compact('vendors'));
    }

    public function create()
    {
        return view('vendors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        Vendor::create([
            'tenant_id' => auth()->user()->tenant_id,
            ...$validated,
        ]);

        return redirect()->route('vendors.index')->with('success', 'Vendor created successfully!');
    }

    public function show(Vendor $vendor)
    {
        $vendor->load('stockTransactions.inventoryItem');
        
        return view('vendors.show', compact('vendor'));
    }

    public function edit(Vendor $vendor)
    {
        return view('vendors.edit', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        $vendor->update($validated);

        return redirect()->route('vendors.index')->with('success', 'Vendor updated successfully!');
    }

    public function destroy(Vendor $vendor)
    {
        $vendor->delete();
        return redirect()->route('vendors.index')->with('success', 'Vendor deleted successfully!');
    }
}

