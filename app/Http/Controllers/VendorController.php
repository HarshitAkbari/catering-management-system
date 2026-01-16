<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Services\VendorService;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function __construct(
        private readonly VendorService $vendorService
    ) {}

    public function index()
    {
        $tenantId = auth()->user()->tenant_id;
        $vendors = $this->vendorService->getByTenant($tenantId);

        // Manual pagination since getByTenant returns Collection
        $currentPage = request()->get('page', 1);
        $perPage = 15;
        $items = $vendors->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $total = $vendors->count();
        
        $vendors = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

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

        $tenantId = auth()->user()->tenant_id;
        $result = $this->vendorService->create(array_merge($validated, ['tenant_id' => $tenantId]));

        if (!$result) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create vendor.']);
        }

        return redirect()->route('vendors.index')->with('success', 'Vendor created successfully!');
    }

    public function show(Vendor $vendor)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($vendor->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $vendor->load('stockTransactions.inventoryItem');
        
        return view('vendors.show', compact('vendor'));
    }

    public function edit(Vendor $vendor)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($vendor->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        return view('vendors.edit', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($vendor->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        $this->vendorService->update($vendor, $validated);

        return redirect()->route('vendors.index')->with('success', 'Vendor updated successfully!');
    }

    public function destroy(Vendor $vendor)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($vendor->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $this->vendorService->delete($vendor);
        return redirect()->route('vendors.index')->with('success', 'Vendor deleted successfully!');
    }
}
