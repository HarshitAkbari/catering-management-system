<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Order;
use App\Services\EquipmentService;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function __construct(
        private readonly EquipmentService $equipmentService
    ) {}

    public function index()
    {
        $tenantId = auth()->user()->tenant_id;
        $equipment = $this->equipmentService->getByTenant($tenantId);

        // Manual pagination
        $currentPage = request()->get('page', 1);
        $perPage = 15;
        $items = $equipment->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $total = $equipment->count();
        
        $equipment = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

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
            'available_quantity' => [
                'required',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value > $request->input('quantity')) {
                        $fail('The available quantity cannot exceed the total quantity.');
                    }
                },
            ],
            'status' => 'required|in:available,damaged',
        ]);

        $tenantId = auth()->user()->tenant_id;
        $result = $this->equipmentService->create(array_merge($validated, ['tenant_id' => $tenantId]));

        if (!$result) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create equipment.']);
        }

        return redirect()->route('equipment.index')->with('success', 'Equipment created successfully!');
    }

    public function show(Equipment $equipment)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($equipment->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $equipment->load('orders');
        
        return view('equipment.show', compact('equipment'));
    }

    public function edit(Equipment $equipment)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($equipment->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        return view('equipment.edit', compact('equipment'));
    }

    public function update(Request $request, Equipment $equipment)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($equipment->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'quantity' => 'required|integer|min:0',
            'available_quantity' => [
                'required',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value > $request->input('quantity')) {
                        $fail('The available quantity cannot exceed the total quantity.');
                    }
                },
            ],
            'status' => 'required|in:available,damaged',
        ]);

        $this->equipmentService->update($equipment, $validated);

        return redirect()->route('equipment.index')->with('success', 'Equipment updated successfully!');
    }

    public function destroy(Equipment $equipment)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($equipment->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $this->equipmentService->delete($equipment);
        return redirect()->route('equipment.index')->with('success', 'Equipment deleted successfully!');
    }

    public function assignToEvent(Request $request, Order $order)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($order->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $equipment = $this->equipmentService->getAvailable($tenantId);
        $assignedEquipment = $order->equipment;

        return view('equipment.assign', compact('order', 'equipment', 'assignedEquipment'));
    }

    public function storeAssignment(Request $request, Order $order)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($order->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'equipment_ids' => 'required|array',
            'equipment_ids.*' => 'exists:equipment,id',
            'quantities' => 'required|array',
            'quantities.*' => 'required|integer|min:1',
        ]);

        $equipmentIds = $validated['equipment_ids'];
        $quantities = $validated['quantities'];

        // Validate that assigned quantities don't exceed available quantities
        foreach ($equipmentIds as $index => $equipmentId) {
            $equipment = $this->equipmentService->getById($equipmentId);
            $requestedQuantity = $quantities[$index] ?? 1;
            
            if ($equipment && $requestedQuantity > $equipment->available_quantity) {
                return back()->withErrors([
                    'quantities.' . $index => "The requested quantity ({$requestedQuantity}) exceeds available quantity ({$equipment->available_quantity}) for {$equipment->name}."
                ])->withInput();
            }
        }

        $syncData = [];
        foreach ($equipmentIds as $index => $equipmentId) {
            $syncData[$equipmentId] = ['quantity' => $quantities[$index] ?? 1];
        }

        $order->equipment()->sync($syncData);

        return redirect()->route('orders.show', $order)->with('success', 'Equipment assigned successfully!');
    }
}
