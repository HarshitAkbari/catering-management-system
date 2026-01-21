<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\EquipmentCategory;
use App\Models\EquipmentStatus;
use App\Models\EventTime;
use App\Models\InventoryUnit;
use App\Models\OrderStatus;
use App\Models\OrderType;
use App\Services\EquipmentService;
use App\Services\SettingsService;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct(
        private readonly SettingsService $settingsService,
        private readonly EquipmentService $equipmentService
    ) {}

    /**
     * Display settings dashboard
     */
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;
        
        $orderStatuses = $this->settingsService->getOrderStatuses($tenantId);
        $eventTimes = $this->settingsService->getEventTimes($tenantId);
        $orderTypes = $this->settingsService->getOrderTypes($tenantId);
        
        $page_title = 'Settings';
        $subtitle = 'Manage order statuses, event times, and order types';
        
        return view('settings.index', compact('orderStatuses', 'eventTimes', 'orderTypes', 'page_title', 'subtitle'));
    }

    // Order Statuses Methods
    public function orderStatuses(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        
        // Build filters from request
        $filters = ['tenant_id' => $tenantId];
        
        // Name filter
        if ($request->has('name_like') && !empty($request->name_like)) {
            $filters['name_like'] = $request->name_like;
        }
        
        // Status filter
        if ($request->has('status') && !empty($request->status)) {
            $filters['is_active'] = $request->status === 'active' ? 1 : 0;
        }
        
        // Sorting parameters
        if ($request->has('sort_by') && !empty($request->sort_by)) {
            $filters['sort_by'] = $request->sort_by;
        }
        if ($request->has('sort_order') && !empty($request->sort_order)) {
            $filters['sort_order'] = $request->sort_order;
        }
        
        $orderStatuses = $this->settingsService->getOrderStatuses($tenantId, 15, $filters);
        
        // Pass filter values to view for form preservation
        $filterValues = [
            'name_like' => $request->input('name_like', ''),
            'status' => $request->input('status', ''),
        ];
        
        $page_title = 'Order Statuses';
        return view('settings.order_statuses.index', compact('orderStatuses', 'filterValues', 'page_title'));
    }

    public function createOrderStatus()
    {
        $page_title = 'Create Order Status';
        return view('settings.order_statuses.create', compact('page_title'));
    }

    public function storeOrderStatus(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $tenantId = auth()->user()->tenant_id;
        $result = $this->settingsService->createOrderStatus($validated, $tenantId);

        if (!$result['status']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $result['message']]);
        }

        return redirect()->route('settings.order-statuses')
            ->with('success', 'Order status created successfully!');
    }

    public function editOrderStatus(OrderStatus $orderStatus)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($orderStatus->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $page_title = 'Edit Order Status';
        return view('settings.order_statuses.edit', compact('orderStatus', 'page_title'));
    }

    public function updateOrderStatus(Request $request, OrderStatus $orderStatus)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($orderStatus->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $result = $this->settingsService->updateOrderStatus($orderStatus, $validated);

        if (!$result['status']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $result['message']]);
        }

        return redirect()->route('settings.order-statuses')
            ->with('success', 'Order status updated successfully!');
    }

    public function destroyOrderStatus(OrderStatus $orderStatus)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($orderStatus->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $result = $this->settingsService->deleteOrderStatus($orderStatus);

        if (!$result['status']) {
            return redirect()->route('settings.order-statuses')
                ->with('error', $result['message']);
        }

        return redirect()->route('settings.order-statuses')
            ->with('success', 'Order status deleted successfully!');
    }

    public function toggleOrderStatus(OrderStatus $orderStatus)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($orderStatus->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $result = $this->settingsService->toggleOrderStatus($orderStatus);

        if (!$result['status']) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], 422);
            }

            return redirect()->route('settings.order-statuses')
                ->with('error', $result['message']);
        }

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $result['is_active'],
                'message' => $result['is_active'] 
                    ? 'Order status activated successfully!' 
                    : 'Order status deactivated successfully!',
            ]);
        }

        return redirect()->route('settings.order-statuses')
            ->with('success', $result['is_active'] 
                ? 'Order status activated successfully!' 
                : 'Order status deactivated successfully!');
    }

    // Event Times Methods
    public function eventTimes(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        
        // Build filters from request
        $filters = ['tenant_id' => $tenantId];
        
        // Name filter
        if ($request->has('name_like') && !empty($request->name_like)) {
            $filters['name_like'] = $request->name_like;
        }
        
        // Status filter
        if ($request->has('status') && !empty($request->status)) {
            $filters['is_active'] = $request->status === 'active' ? 1 : 0;
        }
        
        // Sorting parameters
        if ($request->has('sort_by') && !empty($request->sort_by)) {
            $filters['sort_by'] = $request->sort_by;
        }
        if ($request->has('sort_order') && !empty($request->sort_order)) {
            $filters['sort_order'] = $request->sort_order;
        }
        
        $eventTimes = $this->settingsService->getEventTimes($tenantId, 15, $filters);
        
        // Pass filter values to view for form preservation
        $filterValues = [
            'name_like' => $request->input('name_like', ''),
            'status' => $request->input('status', ''),
        ];
        
        $page_title = 'Event Times';
        return view('settings.event_times.index', compact('eventTimes', 'filterValues', 'page_title'));
    }

    public function createEventTime()
    {
        $page_title = 'Create Event Time';
        return view('settings.event_times.create', compact('page_title'));
    }

    public function storeEventTime(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $tenantId = auth()->user()->tenant_id;
        $result = $this->settingsService->createEventTime($validated, $tenantId);

        if (!$result['status']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $result['message']]);
        }

        return redirect()->route('settings.event-times')
            ->with('success', 'Event time created successfully!');
    }

    public function editEventTime(EventTime $eventTime)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($eventTime->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $page_title = 'Edit Event Time';
        return view('settings.event_times.edit', compact('eventTime', 'page_title'));
    }

    public function updateEventTime(Request $request, EventTime $eventTime)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($eventTime->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $result = $this->settingsService->updateEventTime($eventTime, $validated);

        if (!$result['status']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $result['message']]);
        }

        return redirect()->route('settings.event-times')
            ->with('success', 'Event time updated successfully!');
    }

    public function destroyEventTime(EventTime $eventTime)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($eventTime->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $result = $this->settingsService->deleteEventTime($eventTime);

        if (!$result['status']) {
            return redirect()->route('settings.event-times')
                ->with('error', $result['message']);
        }

        return redirect()->route('settings.event-times')
            ->with('success', 'Event time deleted successfully!');
    }

    public function toggleEventTime(EventTime $eventTime)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($eventTime->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $result = $this->settingsService->toggleEventTime($eventTime);

        if (!$result['status']) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], 422);
            }

            return redirect()->route('settings.event-times')
                ->with('error', $result['message']);
        }

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $result['is_active'],
                'message' => $result['is_active'] 
                    ? 'Event time activated successfully!' 
                    : 'Event time deactivated successfully!',
            ]);
        }

        return redirect()->route('settings.event-times')
            ->with('success', $result['is_active'] 
                ? 'Event time activated successfully!' 
                : 'Event time deactivated successfully!');
    }

    // Order Types Methods
    public function orderTypes(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        
        // Build filters from request
        $filters = ['tenant_id' => $tenantId];
        
        // Name filter
        if ($request->has('name_like') && !empty($request->name_like)) {
            $filters['name_like'] = $request->name_like;
        }
        
        // Status filter
        if ($request->has('status') && !empty($request->status)) {
            $filters['is_active'] = $request->status === 'active' ? 1 : 0;
        }
        
        // Sorting parameters
        if ($request->has('sort_by') && !empty($request->sort_by)) {
            $filters['sort_by'] = $request->sort_by;
        }
        if ($request->has('sort_order') && !empty($request->sort_order)) {
            $filters['sort_order'] = $request->sort_order;
        }
        
        $orderTypes = $this->settingsService->getOrderTypes($tenantId, 15, $filters);
        
        // Pass filter values to view for form preservation
        $filterValues = [
            'name_like' => $request->input('name_like', ''),
            'status' => $request->input('status', ''),
        ];
        
        $page_title = 'Order Types';
        return view('settings.order_types.index', compact('orderTypes', 'filterValues', 'page_title'));
    }

    public function createOrderType()
    {
        $page_title = 'Create Order Type';
        return view('settings.order_types.create', compact('page_title'));
    }

    public function storeOrderType(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $tenantId = auth()->user()->tenant_id;
        $result = $this->settingsService->createOrderType($validated, $tenantId);

        if (!$result['status']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $result['message']]);
        }

        return redirect()->route('settings.order-types')
            ->with('success', 'Order type created successfully!');
    }

    public function editOrderType(OrderType $orderType)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($orderType->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $page_title = 'Edit Order Type';
        return view('settings.order_types.edit', compact('orderType', 'page_title'));
    }

    public function updateOrderType(Request $request, OrderType $orderType)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($orderType->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $result = $this->settingsService->updateOrderType($orderType, $validated);

        if (!$result['status']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $result['message']]);
        }

        return redirect()->route('settings.order-types')
            ->with('success', 'Order type updated successfully!');
    }

    public function destroyOrderType(OrderType $orderType)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($orderType->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $result = $this->settingsService->deleteOrderType($orderType);

        if (!$result['status']) {
            return redirect()->route('settings.order-types')
                ->with('error', $result['message']);
        }

        return redirect()->route('settings.order-types')
            ->with('success', 'Order type deleted successfully!');
    }

    public function toggleOrderType(OrderType $orderType)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($orderType->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $result = $this->settingsService->toggleOrderType($orderType);

        if (!$result['status']) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], 422);
            }

            return redirect()->route('settings.order-types')
                ->with('error', $result['message']);
        }

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $result['is_active'],
                'message' => $result['is_active'] 
                    ? 'Order type activated successfully!' 
                    : 'Order type deactivated successfully!',
            ]);
        }

        return redirect()->route('settings.order-types')
            ->with('success', $result['is_active'] 
                ? 'Order type activated successfully!' 
                : 'Order type deactivated successfully!');
    }

    // Inventory Units Methods
    public function inventoryUnits(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        
        // Build filters from request
        $filters = ['tenant_id' => $tenantId];
        
        // Name filter
        if ($request->has('name_like') && !empty($request->name_like)) {
            $filters['name_like'] = $request->name_like;
        }
        
        // Status filter
        if ($request->has('status') && !empty($request->status)) {
            $filters['is_active'] = $request->status === 'active' ? 1 : 0;
        }
        
        // Sorting parameters
        if ($request->has('sort_by') && !empty($request->sort_by)) {
            $filters['sort_by'] = $request->sort_by;
        }
        if ($request->has('sort_order') && !empty($request->sort_order)) {
            $filters['sort_order'] = $request->sort_order;
        }
        
        $inventoryUnits = $this->settingsService->getInventoryUnits($tenantId, 15, $filters);
        
        // Pass filter values to view for form preservation
        $filterValues = [
            'name_like' => $request->input('name_like', ''),
            'status' => $request->input('status', ''),
        ];
        
        $page_title = 'Inventory Units';
        return view('settings.inventory_units.index', compact('inventoryUnits', 'filterValues', 'page_title'));
    }

    public function createInventoryUnit()
    {
        $page_title = 'Create Inventory Unit';
        return view('settings.inventory_units.create', compact('page_title'));
    }

    public function storeInventoryUnit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $tenantId = auth()->user()->tenant_id;
        $result = $this->settingsService->createInventoryUnit($validated, $tenantId);

        if (!$result['status']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $result['message']]);
        }

        return redirect()->route('settings.inventory-units')
            ->with('success', 'Inventory unit created successfully!');
    }

    public function editInventoryUnit(InventoryUnit $inventoryUnit)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($inventoryUnit->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $page_title = 'Edit Inventory Unit';
        return view('settings.inventory_units.edit', compact('inventoryUnit', 'page_title'));
    }

    public function updateInventoryUnit(Request $request, InventoryUnit $inventoryUnit)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($inventoryUnit->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $result = $this->settingsService->updateInventoryUnit($inventoryUnit, $validated);

        if (!$result['status']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $result['message']]);
        }

        return redirect()->route('settings.inventory-units')
            ->with('success', 'Inventory unit updated successfully!');
    }

    public function destroyInventoryUnit(InventoryUnit $inventoryUnit)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($inventoryUnit->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $result = $this->settingsService->deleteInventoryUnit($inventoryUnit);

        if (!$result['status']) {
            return redirect()->route('settings.inventory-units')
                ->with('error', $result['message']);
        }

        return redirect()->route('settings.inventory-units')
            ->with('success', 'Inventory unit deleted successfully!');
    }

    public function toggleInventoryUnit(InventoryUnit $inventoryUnit)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($inventoryUnit->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $result = $this->settingsService->toggleInventoryUnit($inventoryUnit);

        if (!$result['status']) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], 422);
            }

            return redirect()->route('settings.inventory-units')
                ->with('error', $result['message']);
        }

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $result['is_active'],
                'message' => $result['is_active'] 
                    ? 'Inventory unit activated successfully!' 
                    : 'Inventory unit deactivated successfully!',
            ]);
        }

        return redirect()->route('settings.inventory-units')
            ->with('success', $result['is_active'] 
                ? 'Inventory unit activated successfully!' 
                : 'Inventory unit deactivated successfully!');
    }

    // Equipment Categories Methods
    public function equipmentCategories(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        
        // Build filters from request
        $filters = ['tenant_id' => $tenantId];
        
        // Name filter
        if ($request->has('name_like') && !empty($request->name_like)) {
            $filters['name_like'] = $request->name_like;
        }
        
        // Status filter
        if ($request->has('status') && !empty($request->status)) {
            $filters['is_active'] = $request->status === 'active' ? 1 : 0;
        }
        
        // Sorting parameters
        if ($request->has('sort_by') && !empty($request->sort_by)) {
            $filters['sort_by'] = $request->sort_by;
        }
        if ($request->has('sort_order') && !empty($request->sort_order)) {
            $filters['sort_order'] = $request->sort_order;
        }
        
        $equipmentCategories = $this->equipmentService->getEquipmentCategories($tenantId, 15, $filters);
        
        // Pass filter values to view for form preservation
        $filterValues = [
            'name_like' => $request->input('name_like', ''),
            'status' => $request->input('status', ''),
        ];
        
        $page_title = 'Equipment Categories';
        return view('settings.equipment_categories.index', compact('equipmentCategories', 'filterValues', 'page_title'));
    }

    public function createEquipmentCategory()
    {
        $page_title = 'Create Equipment Category';
        return view('settings.equipment_categories.create', compact('page_title'));
    }

    public function storeEquipmentCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $tenantId = auth()->user()->tenant_id;
        $result = $this->equipmentService->createEquipmentCategory($validated, $tenantId);

        if (!$result['status']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $result['message']]);
        }

        return redirect()->route('settings.equipment-categories')
            ->with('success', 'Equipment category created successfully!');
    }

    public function editEquipmentCategory(EquipmentCategory $equipmentCategory)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($equipmentCategory->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $page_title = 'Edit Equipment Category';
        return view('settings.equipment_categories.edit', compact('equipmentCategory', 'page_title'));
    }

    public function updateEquipmentCategory(Request $request, EquipmentCategory $equipmentCategory)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($equipmentCategory->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $result = $this->equipmentService->updateEquipmentCategory($equipmentCategory, $validated);

        if (!$result['status']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $result['message']]);
        }

        return redirect()->route('settings.equipment-categories')
            ->with('success', 'Equipment category updated successfully!');
    }

    public function destroyEquipmentCategory(EquipmentCategory $equipmentCategory)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($equipmentCategory->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $result = $this->equipmentService->deleteEquipmentCategory($equipmentCategory);

        if (!$result['status']) {
            return redirect()->route('settings.equipment-categories')
                ->with('error', $result['message']);
        }

        return redirect()->route('settings.equipment-categories')
            ->with('success', 'Equipment category deleted successfully!');
    }

    public function toggleEquipmentCategory(EquipmentCategory $equipmentCategory)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($equipmentCategory->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $result = $this->equipmentService->toggleEquipmentCategory($equipmentCategory);

        if (!$result['status']) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], 422);
            }

            return redirect()->route('settings.equipment-categories')
                ->with('error', $result['message']);
        }

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $result['is_active'],
                'message' => $result['is_active'] 
                    ? 'Equipment category activated successfully!' 
                    : 'Equipment category deactivated successfully!',
            ]);
        }

        return redirect()->route('settings.equipment-categories')
            ->with('success', $result['is_active'] 
                ? 'Equipment category activated successfully!' 
                : 'Equipment category deactivated successfully!');
    }

    // Equipment Statuses Methods
    public function equipmentStatuses(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        
        // Build filters from request
        $filters = ['tenant_id' => $tenantId];
        
        // Name filter
        if ($request->has('name_like') && !empty($request->name_like)) {
            $filters['name_like'] = $request->name_like;
        }
        
        // Status filter
        if ($request->has('status') && !empty($request->status)) {
            $filters['is_active'] = $request->status === 'active' ? 1 : 0;
        }
        
        // Sorting parameters
        if ($request->has('sort_by') && !empty($request->sort_by)) {
            $filters['sort_by'] = $request->sort_by;
        }
        if ($request->has('sort_order') && !empty($request->sort_order)) {
            $filters['sort_order'] = $request->sort_order;
        }
        
        $equipmentStatuses = $this->equipmentService->getEquipmentStatuses($tenantId, 15, $filters);
        
        // Pass filter values to view for form preservation
        $filterValues = [
            'name_like' => $request->input('name_like', ''),
            'status' => $request->input('status', ''),
        ];
        
        $page_title = 'Equipment Statuses';
        return view('settings.equipment_statuses.index', compact('equipmentStatuses', 'filterValues', 'page_title'));
    }

    public function createEquipmentStatus()
    {
        $page_title = 'Create Equipment Status';
        return view('settings.equipment_statuses.create', compact('page_title'));
    }

    public function storeEquipmentStatus(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $tenantId = auth()->user()->tenant_id;
        $result = $this->equipmentService->createEquipmentStatus($validated, $tenantId);

        if (!$result['status']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $result['message']]);
        }

        return redirect()->route('settings.equipment-statuses')
            ->with('success', 'Equipment status created successfully!');
    }

    public function editEquipmentStatus(EquipmentStatus $equipmentStatus)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($equipmentStatus->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $page_title = 'Edit Equipment Status';
        return view('settings.equipment_statuses.edit', compact('equipmentStatus', 'page_title'));
    }

    public function updateEquipmentStatus(Request $request, EquipmentStatus $equipmentStatus)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($equipmentStatus->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $result = $this->equipmentService->updateEquipmentStatus($equipmentStatus, $validated);

        if (!$result['status']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $result['message']]);
        }

        return redirect()->route('settings.equipment-statuses')
            ->with('success', 'Equipment status updated successfully!');
    }

    public function destroyEquipmentStatus(EquipmentStatus $equipmentStatus)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($equipmentStatus->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $result = $this->equipmentService->deleteEquipmentStatus($equipmentStatus);

        if (!$result['status']) {
            return redirect()->route('settings.equipment-statuses')
                ->with('error', $result['message']);
        }

        return redirect()->route('settings.equipment-statuses')
            ->with('success', 'Equipment status deleted successfully!');
    }

    public function toggleEquipmentStatus(EquipmentStatus $equipmentStatus)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($equipmentStatus->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $result = $this->equipmentService->toggleEquipmentStatus($equipmentStatus);

        if (!$result['status']) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], 422);
            }

            return redirect()->route('settings.equipment-statuses')
                ->with('error', $result['message']);
        }

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $result['is_active'],
                'message' => $result['is_active'] 
                    ? 'Equipment status activated successfully!' 
                    : 'Equipment status deactivated successfully!',
            ]);
        }

        return redirect()->route('settings.equipment-statuses')
            ->with('success', $result['is_active'] 
                ? 'Equipment status activated successfully!' 
                : 'Equipment status deactivated successfully!');
    }
}

