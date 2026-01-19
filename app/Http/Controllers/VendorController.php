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

    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        
        // Build filters from request
        $filters = ['tenant_id' => $tenantId];
        
        // Name filter
        if ($request->has('name_like') && !empty($request->name_like)) {
            $filters['name_like'] = $request->name_like;
        }
        
        // Contact person filter
        if ($request->has('contact_person_like') && !empty($request->contact_person_like)) {
            $filters['contact_person_like'] = $request->contact_person_like;
        }
        
        // Email filter
        if ($request->has('email_like') && !empty($request->email_like)) {
            $filters['email_like'] = $request->email_like;
        }
        
        // Sorting parameters
        if ($request->has('sort_by') && !empty($request->sort_by)) {
            $filters['sort_by'] = $request->sort_by;
        }
        if ($request->has('sort_order') && !empty($request->sort_order)) {
            $filters['sort_order'] = $request->sort_order;
        }
        
        $vendors = $this->vendorService->getByTenant($tenantId, 15, $filters);
        
        // Pass filter values to view for form preservation
        $filterValues = [
            'name_like' => $request->input('name_like', ''),
            'contact_person_like' => $request->input('contact_person_like', ''),
            'email_like' => $request->input('email_like', ''),
        ];
        
        $page_title = 'Vendors';
        $subtitle = 'Manage your vendor database';
        
        return view('vendors.index', compact('vendors', 'filterValues', 'page_title', 'subtitle'));
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
