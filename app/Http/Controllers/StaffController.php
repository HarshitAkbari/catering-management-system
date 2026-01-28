<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Staff;
use App\Services\StaffService;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function __construct(
        private readonly StaffService $staffService
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
        
        // Phone filter
        if ($request->has('phone_like') && !empty($request->phone_like)) {
            $filters['phone_like'] = $request->phone_like;
        }
        
        // Email filter
        if ($request->has('email_like') && !empty($request->email_like)) {
            $filters['email_like'] = $request->email_like;
        }
        
        // Role filter
        if ($request->has('staff_role') && !empty($request->staff_role)) {
            $filters['staff_role'] = $request->staff_role;
        }
        
        // Status filter
        if ($request->has('status') && !empty($request->status)) {
            $filters['status'] = $request->status;
        }
        
        // Sorting parameters
        if ($request->has('sort_by') && !empty($request->sort_by)) {
            $filters['sort_by'] = $request->sort_by;
        }
        if ($request->has('sort_order') && !empty($request->sort_order)) {
            $filters['sort_order'] = $request->sort_order;
        }
        
        $staff = $this->staffService->getByTenant($tenantId, 15, $filters);
        
        // Load relationships and calculate total events for each staff
        $staff->loadCount('orders');
        
        // Pass filter values to view for form preservation
        $filterValues = [
            'name_like' => $request->input('name_like', ''),
            'phone_like' => $request->input('phone_like', ''),
            'email_like' => $request->input('email_like', ''),
            'staff_role' => $request->input('staff_role', ''),
            'status' => $request->input('status', ''),
        ];
        
        // Get unique roles for filter dropdown (legacy support - still using staff_role string field for filtering)
        $roles = Staff::where('tenant_id', $tenantId)
            ->distinct()
            ->pluck('staff_role')
            ->filter()
            ->values()
            ->toArray();
        
        $page_title = 'Staff';
        $subtitle = 'Manage your staff members';
        
        return view('staff.index', compact('staff', 'filterValues', 'roles', 'page_title', 'subtitle'));
    }

    public function create()
    {
        $tenantId = auth()->user()->tenant_id;
        $roles = $this->staffService->getStaffRoles($tenantId);
        
        return view('staff.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'staff_role_id' => 'required|exists:staff_roles,id',
            'address' => 'nullable|string',
        ]);
        
        // Get the role name from staff_role_id
        $staffRole = \App\Models\StaffRole::find($validated['staff_role_id']);
        if ($staffRole && $staffRole->tenant_id === auth()->user()->tenant_id) {
            $validated['staff_role'] = $staffRole->name;
        } else {
            return redirect()->back()
                ->withInput()
                ->withErrors(['staff_role_id' => 'Invalid role selected.']);
        }

        $tenantId = auth()->user()->tenant_id;
        
        // Check for unique phone per tenant
        $existingStaff = Staff::where('tenant_id', $tenantId)
            ->where('phone', $validated['phone'])
            ->first();
            
        if ($existingStaff) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['phone' => 'A staff member with this phone number already exists.']);
        }

        // Check for unique email per tenant if provided
        if (!empty($validated['email'])) {
            $existingStaff = Staff::where('tenant_id', $tenantId)
                ->where('email', $validated['email'])
                ->first();
                
            if ($existingStaff) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['email' => 'A staff member with this email already exists.']);
            }
        }

        $this->staffService->create(array_merge($validated, ['tenant_id' => $tenantId]));

        return redirect()->route('staff.index')->with('success', 'Staff member created successfully!');
    }

    public function show(Staff $staff)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($staff->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $staff->load(['orders.customer', 'orders.orderStatus', 'orders.eventTime', 'attendances']);
        
        // Get upcoming events
        $upcomingEvents = $staff->getUpcomingEvents();
        
        // Get past events
        $pastEvents = $staff->orders()
            ->where('event_date', '<', now()->toDateString())
            ->with(['customer', 'orderStatus', 'eventTime'])
            ->orderBy('event_date', 'desc')
            ->limit(10)
            ->get();
        
        // Get attendance summary
        $attendanceStats = $staff->attendances()
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN status = "half_day" THEN 1 ELSE 0 END) as half_day
            ')
            ->first();
        
        $attendanceRate = $staff->getAttendanceRate();
        
        return view('staff.show', compact('staff', 'upcomingEvents', 'pastEvents', 'attendanceStats', 'attendanceRate'));
    }

    public function edit(Staff $staff)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($staff->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $roles = $this->staffService->getStaffRoles($tenantId);

        return view('staff.edit', compact('staff', 'roles'));
    }

    public function update(Request $request, Staff $staff)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($staff->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'staff_role_id' => 'required|exists:staff_roles,id',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);
        
        // Get the role name from staff_role_id
        $staffRole = \App\Models\StaffRole::find($validated['staff_role_id']);
        if ($staffRole && $staffRole->tenant_id === auth()->user()->tenant_id) {
            $validated['staff_role'] = $staffRole->name;
        } else {
            return redirect()->back()
                ->withInput()
                ->withErrors(['staff_role_id' => 'Invalid role selected.']);
        }

        // Check for unique phone per tenant (excluding current staff)
        $existingStaff = Staff::where('tenant_id', $tenantId)
            ->where('phone', $validated['phone'])
            ->where('id', '!=', $staff->id)
            ->first();
            
        if ($existingStaff) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['phone' => 'A staff member with this phone number already exists.']);
        }

        // Check for unique email per tenant if provided (excluding current staff)
        if (!empty($validated['email'])) {
            $existingStaff = Staff::where('tenant_id', $tenantId)
                ->where('email', $validated['email'])
                ->where('id', '!=', $staff->id)
                ->first();
                
            if ($existingStaff) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['email' => 'A staff member with this email already exists.']);
            }
        }

        $this->staffService->update($staff, $validated);

        return redirect()->route('staff.show', $staff)->with('success', 'Staff member updated successfully!');
    }

    public function destroy(Staff $staff)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($staff->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        // Check if staff has active event assignments
        $activeAssignments = $staff->orders()
            ->where('event_date', '>=', now()->toDateString())
            ->count();

        if ($activeAssignments > 0) {
            return redirect()->back()
                ->withErrors(['error' => 'Cannot delete staff member with active event assignments.']);
        }

        $this->staffService->delete($staff);
        
        return redirect()->route('staff.index')->with('success', 'Staff member deleted successfully!');
    }

    public function toggle(Staff $staff)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($staff->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $result = $this->staffService->toggleStatus($staff);

        if (!$result['status']) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], 422);
            }

            return redirect()->route('staff.index')
                ->with('error', $result['message']);
        }

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'status' => $result['status'],
                'message' => $result['status'] === 'active' 
                    ? 'Staff member activated successfully!' 
                    : 'Staff member deactivated successfully!',
            ]);
        }

        return redirect()->route('staff.index')
            ->with('success', $result['status'] === 'active' 
                ? 'Staff member activated successfully!' 
                : 'Staff member deactivated successfully!');
    }

    public function assignToEvent(Request $request, Order $order)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($order->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        // Get available staff for this event date
        $availableStaff = $this->staffService->getAvailableStaff(
            $tenantId,
            $order->event_date->toDateString(),
            $order->event_time_id
        );
        
        $assignedStaff = $order->staff;

        return view('staff.assign', compact('order', 'availableStaff', 'assignedStaff'));
    }

    public function storeAssignment(Request $request, Order $order)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($order->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'staff_ids' => 'required|array|min:1',
            'staff_ids.*' => 'exists:staff,id',
            'roles' => 'required|array',
            'roles.*' => 'required|string|max:50',
            'notes' => 'nullable|array',
            'notes.*' => 'nullable|string|max:500',
        ]);

        $staffData = [];
        foreach ($validated['staff_ids'] as $index => $staffId) {
            $staffData[] = [
                'staff_id' => $staffId,
                'role' => $validated['roles'][$index] ?? '',
                'notes' => $validated['notes'][$index] ?? null,
            ];
        }

        $this->staffService->assignToEvent($order->id, $staffData);

        return redirect()->route('orders.show', $order)->with('success', 'Staff assigned successfully!');
    }

    public function workload(Request $request, Staff $staff)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($staff->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $workload = $this->staffService->getStaffWorkload($staff->id, $startDate, $endDate);

        return view('staff.workload', compact('staff', 'workload', 'startDate', 'endDate'));
    }

    public function performance(Request $request, Staff $staff)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($staff->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $startDate = $request->get('start_date', now()->subMonths(6)->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $performance = $this->staffService->getStaffPerformance($staff->id, $startDate, $endDate);

        return view('staff.performance', compact('staff', 'performance', 'startDate', 'endDate'));
    }
}
