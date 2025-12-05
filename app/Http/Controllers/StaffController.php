<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Order;
use App\Models\Staff;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        $staff = Staff::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->paginate(15);

        return view('staff.index', compact('staff'));
    }

    public function create()
    {
        return view('staff.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'role' => 'required|string|max:255',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        Staff::create([
            'tenant_id' => auth()->user()->tenant_id,
            ...$validated,
        ]);

        return redirect()->route('staff.index')->with('success', 'Staff member created successfully!');
    }

    public function show(Staff $staff)
    {
        $staff->load('orders', 'attendance');
        
        return view('staff.show', compact('staff'));
    }

    public function edit(Staff $staff)
    {
        return view('staff.edit', compact('staff'));
    }

    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'role' => 'required|string|max:255',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $staff->update($validated);

        return redirect()->route('staff.index')->with('success', 'Staff member updated successfully!');
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();
        return redirect()->route('staff.index')->with('success', 'Staff member deleted successfully!');
    }

    public function assignToEvent(Request $request, Order $order)
    {
        $staff = Staff::where('tenant_id', auth()->user()->tenant_id)
            ->where('status', 'active')
            ->get();

        $assignedStaff = $order->staff;

        return view('staff.assign', compact('order', 'staff', 'assignedStaff'));
    }

    public function storeAssignment(Request $request, Order $order)
    {
        $validated = $request->validate([
            'staff_ids' => 'required|array',
            'staff_ids.*' => 'exists:staff,id',
            'roles' => 'nullable|array',
            'roles.*' => 'nullable|string|max:255',
        ]);

        $staffIds = $validated['staff_ids'];
        $roles = $validated['roles'] ?? [];

        $syncData = [];
        foreach ($staffIds as $index => $staffId) {
            $syncData[$staffId] = ['role' => $roles[$index] ?? null];
        }

        $order->staff()->sync($syncData);

        return redirect()->route('orders.show', $order)->with('success', 'Staff assigned successfully!');
    }

    public function attendance(Request $request)
    {
        $staff = Staff::where('tenant_id', auth()->user()->tenant_id)
            ->where('status', 'active')
            ->get();

        $selectedStaff = $request->get('staff_id');
        $selectedDate = $request->get('date', now()->format('Y-m-d'));

        $attendance = null;
        if ($selectedStaff && $selectedDate) {
            $attendance = Attendance::where('tenant_id', auth()->user()->tenant_id)
                ->where('staff_id', $selectedStaff)
                ->where('date', $selectedDate)
                ->first();
        }

        $recentAttendance = Attendance::where('tenant_id', auth()->user()->tenant_id)
            ->with('staff')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('staff.attendance', compact('staff', 'selectedStaff', 'selectedDate', 'attendance', 'recentAttendance'));
    }

    public function storeAttendance(Request $request)
    {
        $validated = $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent',
            'notes' => 'nullable|string',
        ]);

        Attendance::updateOrCreate(
            [
                'tenant_id' => auth()->user()->tenant_id,
                'staff_id' => $validated['staff_id'],
                'date' => $validated['date'],
            ],
            [
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return redirect()->route('staff.attendance')->with('success', 'Attendance recorded successfully!');
    }
}

