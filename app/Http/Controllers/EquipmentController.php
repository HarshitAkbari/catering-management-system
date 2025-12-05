<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Order;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipment = Equipment::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->paginate(15);

        return view('equipment.index', compact('equipment'));
    }

    public function create()
    {
        return view('equipment.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'quantity' => 'required|integer|min:0',
            'available_quantity' => 'required|integer|min:0',
            'status' => 'required|in:available,maintenance,damaged',
            'last_maintenance_date' => 'nullable|date',
            'next_maintenance_date' => 'nullable|date',
        ]);

        Equipment::create([
            'tenant_id' => auth()->user()->tenant_id,
            ...$validated,
        ]);

        return redirect()->route('equipment.index')->with('success', 'Equipment created successfully!');
    }

    public function show(Equipment $equipment)
    {
        $equipment->load('orders');
        
        return view('equipment.show', compact('equipment'));
    }

    public function edit(Equipment $equipment)
    {
        return view('equipment.edit', compact('equipment'));
    }

    public function update(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'quantity' => 'required|integer|min:0',
            'available_quantity' => 'required|integer|min:0',
            'status' => 'required|in:available,maintenance,damaged',
            'last_maintenance_date' => 'nullable|date',
            'next_maintenance_date' => 'nullable|date',
        ]);

        $equipment->update($validated);

        return redirect()->route('equipment.index')->with('success', 'Equipment updated successfully!');
    }

    public function destroy(Equipment $equipment)
    {
        $equipment->delete();
        return redirect()->route('equipment.index')->with('success', 'Equipment deleted successfully!');
    }

    public function assignToEvent(Request $request, Order $order)
    {
        $equipment = Equipment::where('tenant_id', auth()->user()->tenant_id)
            ->where('status', 'available')
            ->get();

        $assignedEquipment = $order->equipment;

        return view('equipment.assign', compact('order', 'equipment', 'assignedEquipment'));
    }

    public function storeAssignment(Request $request, Order $order)
    {
        $validated = $request->validate([
            'equipment_ids' => 'required|array',
            'equipment_ids.*' => 'exists:equipment,id',
            'quantities' => 'required|array',
            'quantities.*' => 'required|integer|min:1',
        ]);

        $equipmentIds = $validated['equipment_ids'];
        $quantities = $validated['quantities'];

        $syncData = [];
        foreach ($equipmentIds as $index => $equipmentId) {
            $syncData[$equipmentId] = ['quantity' => $quantities[$index] ?? 1];
        }

        $order->equipment()->sync($syncData);

        return redirect()->route('orders.show', $order)->with('success', 'Equipment assigned successfully!');
    }

    public function maintenance()
    {
        $equipment = Equipment::where('tenant_id', auth()->user()->tenant_id)
            ->where(function ($query) {
                $query->where('status', 'maintenance')
                    ->orWhere('next_maintenance_date', '<=', now());
            })
            ->orderBy('next_maintenance_date')
            ->get();

        return view('equipment.maintenance', compact('equipment'));
    }
}

