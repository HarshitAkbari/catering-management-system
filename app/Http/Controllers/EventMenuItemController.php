<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\EventMenuItem;
use App\Services\EventMenuItemService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EventMenuItemController extends Controller
{
    public function __construct(
        private readonly EventMenuItemService $eventMenuItemService
    ) {}

    /**
     * Display a listing of event menu items
     */
    public function index(Request $request)
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

        $eventMenuItems = $this->eventMenuItemService->getByTenant($tenantId, 15, $filters);

        // Pass filter values to view for form preservation
        $filterValues = [
            'name_like' => $request->input('name_like', ''),
            'status' => $request->input('status', ''),
        ];

        $page_title = 'Event Menu Items';
        return view('event_menu_items.index', compact('eventMenuItems', 'filterValues', 'page_title'));
    }

    /**
     * Show the form for creating a new event menu item
     */
    public function create()
    {
        $page_title = 'Create Event Menu Item';
        return view('event_menu_items.create', compact('page_title'));
    }

    /**
     * Store a newly created event menu item
     */
    public function store(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('event_menu_items')->where(function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                }),
            ],
        ]);

        $result = $this->eventMenuItemService->createItem($validated, $tenantId);

        if (!$result['status']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $result['message']]);
        }

        return redirect()->route('orders.event-menu-items')
            ->with('success', 'Event menu item created successfully!');
    }

    /**
     * Show the form for editing the specified event menu item
     */
    public function edit(EventMenuItem $eventMenuItem)
    {
        $tenantId = auth()->user()->tenant_id;

        if ($eventMenuItem->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $page_title = 'Edit Event Menu Item';
        return view('event_menu_items.edit', compact('eventMenuItem', 'page_title'));
    }

    /**
     * Update the specified event menu item
     */
    public function update(Request $request, EventMenuItem $eventMenuItem)
    {
        $tenantId = auth()->user()->tenant_id;

        if ($eventMenuItem->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('event_menu_items')->where(function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                })->ignore($eventMenuItem->id),
            ],
        ]);

        $result = $this->eventMenuItemService->updateItem($eventMenuItem, $validated, $tenantId);

        if (!$result['status']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $result['message']]);
        }

        return redirect()->route('orders.event-menu-items')
            ->with('success', 'Event menu item updated successfully!');
    }

    /**
     * Remove the specified event menu item
     */
    public function destroy(EventMenuItem $eventMenuItem)
    {
        $tenantId = auth()->user()->tenant_id;

        if ($eventMenuItem->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $result = $this->eventMenuItemService->deleteItem($eventMenuItem, $tenantId);

        if (!$result['status']) {
            return redirect()->route('orders.event-menu-items')
                ->with('error', $result['message']);
        }

        return redirect()->route('orders.event-menu-items')
            ->with('success', 'Event menu item deleted successfully!');
    }

    /**
     * Toggle the active status of the specified event menu item
     */
    public function toggle(EventMenuItem $eventMenuItem)
    {
        $tenantId = auth()->user()->tenant_id;

        if ($eventMenuItem->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $result = $this->eventMenuItemService->toggleStatus($eventMenuItem, $tenantId);

        if (!$result['status']) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], 422);
            }

            return redirect()->route('orders.event-menu-items')
                ->with('error', $result['message']);
        }

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $result['is_active'],
                'message' => $result['is_active']
                    ? 'Event menu item activated successfully!'
                    : 'Event menu item deactivated successfully!',
            ]);
        }

        return redirect()->route('orders.event-menu-items')
            ->with('success', $result['is_active']
                ? 'Event menu item activated successfully!'
                : 'Event menu item deactivated successfully!');
    }
}

