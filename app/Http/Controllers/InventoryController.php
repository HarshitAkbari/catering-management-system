<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Services\InventoryService;
use App\Services\VendorService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function __construct(
        private readonly InventoryService $inventoryService,
        private readonly VendorService $vendorService
    ) {}

    public function index()
    {
        $tenantId = auth()->user()->tenant_id;
        $inventoryItems = $this->inventoryService->getByTenant($tenantId);

        return view('inventory.index', compact('inventoryItems'));
    }

    public function create()
    {
        return view('inventory.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
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

        return view('inventory.edit', compact('inventoryItem'));
    }

    public function update(Request $request, InventoryItem $inventoryItem)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
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
        $validated = $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'quantity' => 'required|numeric|min:0.01',
            'price' => 'nullable|numeric|min:0',
            'vendor_id' => 'nullable|exists:vendors,id',
            'notes' => 'nullable|string',
        ]);

        $tenantId = auth()->user()->tenant_id;
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
        $validated = $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
        ]);

        $tenantId = auth()->user()->tenant_id;
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
