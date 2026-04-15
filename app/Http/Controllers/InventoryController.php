<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\Vendor;
use App\Services\InventoryService;
use App\Services\VendorService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function __construct(
        private readonly InventoryService $inventoryService,
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
        
        // Unit filter
        if ($request->has('inventory_unit_id') && !empty($request->inventory_unit_id)) {
            $filters['inventory_unit_id'] = $request->inventory_unit_id;
        }
        
        // Stock status filter (low stock)
        if ($request->has('stock_status') && !empty($request->stock_status)) {
            if ($request->stock_status === 'low') {
                // This will be handled in the service/repository
                $filters['stock_status'] = 'low';
            }
        }
        
        // Sorting parameters
        if ($request->has('sort_by') && !empty($request->sort_by)) {
            $filters['sort_by'] = $request->sort_by;
        }
        if ($request->has('sort_order') && !empty($request->sort_order)) {
            $filters['sort_order'] = $request->sort_order;
        }
        
        $inventoryItems = $this->inventoryService->getByTenant($tenantId, 15, $filters);
        
        // Pass filter values to view for form preservation
        $filterValues = [
            'name_like' => $request->input('name_like', ''),
            'inventory_unit_id' => $request->input('inventory_unit_id', ''),
            'stock_status' => $request->input('stock_status', ''),
        ];
        
        // Get inventory units for filter dropdown
        $inventoryUnits = $this->inventoryService->getInventoryUnits($tenantId);
        
        $page_title = 'Inventory';
        $subtitle = 'Manage your inventory items';
        
        return view('inventory.index', compact('inventoryItems', 'filterValues', 'inventoryUnits', 'page_title', 'subtitle'));
    }

    public function create()
    {
        $tenantId = auth()->user()->tenant_id;
        $inventoryUnits = $this->inventoryService->getInventoryUnits($tenantId);
        $page_title = 'Inventory Item';
        
        return view('inventory.create', compact('inventoryUnits', 'page_title'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'inventory_unit_id' => 'required|exists:inventory_units,id',
            'current_stock' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
            'price_per_unit' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $tenantId = auth()->user()->tenant_id;
        $result = $this->inventoryService->createItem($validated, $tenantId);

        if (!$result['status']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $result['message']]);
        }

        return redirect()->route('inventory.index')->with('success', 'Inventory item created successfully!');
    }

    public function show(InventoryItem $inventoryItem)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($inventoryItem->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $inventoryItem->load('stockTransactions.vendor');
        
        return view('inventory.show', compact('inventoryItem'));
    }

    public function edit(InventoryItem $inventoryItem)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($inventoryItem->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $inventoryUnits = $this->inventoryService->getInventoryUnits($tenantId);

        return view('inventory.edit', compact('inventoryItem', 'inventoryUnits'));
    }

    public function update(Request $request, InventoryItem $inventoryItem)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'inventory_unit_id' => 'required|exists:inventory_units,id',
            'current_stock' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
            'price_per_unit' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $tenantId = auth()->user()->tenant_id;
        $result = $this->inventoryService->updateItem($inventoryItem, $validated, $tenantId);

        if (!$result['status']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $result['message']]);
        }

        return redirect()->route('inventory.index')->with('success', 'Inventory item updated successfully!');
    }

    public function destroy(InventoryItem $inventoryItem)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($inventoryItem->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $this->inventoryService->delete($inventoryItem);
        return redirect()->route('inventory.index')->with('success', 'Inventory item deleted successfully!');
    }

    public function stockIn(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $vendors = $this->vendorService->getByTenant($tenantId);
        $inventoryItems = $this->inventoryService->getAll()->where('tenant_id', $tenantId);

        return view('inventory.stock-in', compact('vendors', 'inventoryItems'));
    }

    public function storeStockIn(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        
        $validated = $request->validate([
            'inventory_item_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($tenantId) {
                    if (!InventoryItem::where('id', $value)->where('tenant_id', $tenantId)->exists()) {
                        $fail('The selected inventory item is invalid.');
                    }
                },
            ],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'vendor_id' => [
                'nullable',
                'integer',
                function ($attribute, $value, $fail) use ($tenantId) {
                    if ($value && !Vendor::where('id', $value)->where('tenant_id', $tenantId)->exists()) {
                        $fail('The selected vendor is invalid.');
                    }
                },
            ],
            'notes' => ['nullable', 'string'],
        ]);

        $result = $this->inventoryService->stockIn($validated, $tenantId);

        if (!$result['status']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $result['message']]);
        }

        return redirect()->route('inventory.index')->with('success', 'Stock added successfully!');
    }

    public function stockOut(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $inventoryItems = $this->inventoryService->getAll()->where('tenant_id', $tenantId);

        return view('inventory.stock-out', compact('inventoryItems'));
    }

    public function storeStockOut(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        
        $validated = $request->validate([
            'inventory_item_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($tenantId) {
                    if (!InventoryItem::where('id', $value)->where('tenant_id', $tenantId)->exists()) {
                        $fail('The selected inventory item is invalid.');
                    }
                },
            ],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string'],
        ]);

        $result = $this->inventoryService->stockOut($validated, $tenantId);

        if (!$result['status']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $result['message']]);
        }

        return redirect()->route('inventory.index')->with('success', 'Stock reduced successfully!');
    }

    public function lowStock()
    {
        $tenantId = auth()->user()->tenant_id;
        $lowStockItems = $this->inventoryService->getLowStock($tenantId);

        return view('inventory.low-stock', compact('lowStockItems'));
    }
}
