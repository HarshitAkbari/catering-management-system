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

    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        
        // Build filters from request
        $filters = ['tenant_id' => $tenantId];
        
        // Name filter
        if ($request->has('name_like') && !empty($request->name_like)) {
            $filters['name_like'] = $request->name_like;
        }
        
        // Category filter
        if ($request->has('equipment_category_id') && !empty($request->equipment_category_id)) {
            $filters['equipment_category_id'] = $request->equipment_category_id;
        }
        
        // Sorting parameters
        if ($request->has('sort_by') && !empty($request->sort_by)) {
            $filters['sort_by'] = $request->sort_by;
        }
        if ($request->has('sort_order') && !empty($request->sort_order)) {
            $filters['sort_order'] = $request->sort_order;
        }
        
        $equipment = $this->equipmentService->getByTenant($tenantId, 15, $filters);
        $equipment->load(['equipmentCategory']);
        
        // Pass filter values to view for form preservation
        $filterValues = [
            'name_like' => $request->input('name_like', ''),
            'equipment_category_id' => $request->input('equipment_category_id', ''),
        ];
        
        // Get categories for filter dropdowns
        $categories = $this->equipmentService->getEquipmentCategories($tenantId);
        
        $page_title = 'Equipment';
        $subtitle = 'Manage your equipment inventory';
        
        return view('equipment.index', compact('equipment', 'filterValues', 'categories', 'page_title', 'subtitle'));
    }

    public function create()
    {
        $tenantId = auth()->user()->tenant_id;
        $categories = $this->equipmentService->getEquipmentCategories($tenantId);
        
        return view('equipment.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'equipment_category_id' => 'nullable|exists:equipment_categories,id',
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

        $equipment->load(['orders', 'equipmentCategory']);
        
        return view('equipment.show', compact('equipment'));
    }

    public function edit(Equipment $equipment)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($equipment->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $categories = $this->equipmentService->getEquipmentCategories($tenantId);

        return view('equipment.edit', compact('equipment', 'categories'));
    }

    public function update(Request $request, Equipment $equipment)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($equipment->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'equipment_category_id' => 'nullable|exists:equipment_categories,id',
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
