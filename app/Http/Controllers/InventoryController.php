<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\StockTransaction;
use App\Models\Vendor;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $inventoryItems = InventoryItem::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->paginate(15);

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

        InventoryItem::create([
            'tenant_id' => auth()->user()->tenant_id,
            ...$validated,
        ]);

        return redirect()->route('inventory.index')->with('success', 'Inventory item created successfully!');
    }

    public function show(InventoryItem $inventoryItem)
    {
        $inventoryItem->load('stockTransactions.vendor');
        
        return view('inventory.show', compact('inventoryItem'));
    }

    public function edit(InventoryItem $inventoryItem)
    {
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

        $inventoryItem->update($validated);

        return redirect()->route('inventory.index')->with('success', 'Inventory item updated successfully!');
    }

    public function destroy(InventoryItem $inventoryItem)
    {
        $inventoryItem->delete();
        return redirect()->route('inventory.index')->with('success', 'Inventory item deleted successfully!');
    }

    public function stockIn(Request $request)
    {
        $vendors = Vendor::where('tenant_id', auth()->user()->tenant_id)->get();
        $inventoryItems = InventoryItem::where('tenant_id', auth()->user()->tenant_id)->get();

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

        $inventoryItem = InventoryItem::findOrFail($validated['inventory_item_id']);

        StockTransaction::create([
            'tenant_id' => auth()->user()->tenant_id,
            'inventory_item_id' => $validated['inventory_item_id'],
            'type' => 'in',
            'quantity' => $validated['quantity'],
            'price' => $validated['price'] ?? null,
            'vendor_id' => $validated['vendor_id'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $inventoryItem->increment('current_stock', $validated['quantity']);

        return redirect()->route('inventory.index')->with('success', 'Stock added successfully!');
    }

    public function stockOut(Request $request)
    {
        $inventoryItems = InventoryItem::where('tenant_id', auth()->user()->tenant_id)->get();

        return view('inventory.stock-out', compact('inventoryItems'));
    }

    public function storeStockOut(Request $request)
    {
        $validated = $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
        ]);

        $inventoryItem = InventoryItem::findOrFail($validated['inventory_item_id']);

        if ($inventoryItem->current_stock < $validated['quantity']) {
            return back()->withErrors(['quantity' => 'Insufficient stock available.']);
        }

        StockTransaction::create([
            'tenant_id' => auth()->user()->tenant_id,
            'inventory_item_id' => $validated['inventory_item_id'],
            'type' => 'out',
            'quantity' => $validated['quantity'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $inventoryItem->decrement('current_stock', $validated['quantity']);

        return redirect()->route('inventory.index')->with('success', 'Stock reduced successfully!');
    }

    public function lowStock()
    {
        $lowStockItems = InventoryItem::where('tenant_id', auth()->user()->tenant_id)
            ->whereRaw('current_stock <= minimum_stock')
            ->orderBy('name')
            ->get();

        return view('inventory.low-stock', compact('lowStockItems'));
    }
}

