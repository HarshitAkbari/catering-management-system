<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Staff;
use App\Services\AttendanceService;
use App\Services\StaffService;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(
        private readonly AttendanceService $attendanceService,
        private readonly StaffService $staffService
    ) {}

    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        
        // Build filters from request
        $filters = ['tenant_id' => $tenantId];
        
        // Date range filter
        if ($request->has('start_date') && !empty($request->start_date)) {
            $filters['date_from'] = $request->start_date;
        }
        if ($request->has('end_date') && !empty($request->end_date)) {
            $filters['date_to'] = $request->end_date;
        }
        
        // Staff filter
        if ($request->has('staff_id') && !empty($request->staff_id)) {
            $filters['staff_id'] = $request->staff_id;
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
        
        $attendances = $this->attendanceService->getByTenant($tenantId, 15, $filters);
        $attendances->load('staff');
        
        // Pass filter values to view for form preservation
        $filterValues = [
            'start_date' => $request->input('start_date', ''),
            'end_date' => $request->input('end_date', ''),
            'staff_id' => $request->input('staff_id', ''),
            'status' => $request->input('status', ''),
        ];
        
        // Get all active staff for filter dropdown
        $staffList = $this->staffService->getAllActive($tenantId);
        
        // Get today's statistics
        $todayStats = $this->attendanceService->getByDate($tenantId, now()->toDateString());
        $todayPresent = $todayStats->where('status', 'present')->count();
        $todayAbsent = $todayStats->where('status', 'absent')->count();
        
        $page_title = 'Attendance';
        $subtitle = 'Track staff attendance';
        
        return view('attendance.index', compact('attendances', 'filterValues', 'staffList', 'todayPresent', 'todayAbsent', 'page_title', 'subtitle'));
    }

    public function create()
    {
        $tenantId = auth()->user()->tenant_id;
        $staffList = $this->staffService->getAllActive($tenantId);
        
        return view('attendance.create', compact('staffList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'date' => 'required|date|before_or_equal:today',
            'status' => 'required|in:present,absent,late,half_day',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i|after:check_in_time',
            'notes' => 'nullable|string|max:500',
        ]);

        $tenantId = auth()->user()->tenant_id;
        
        // Verify staff belongs to tenant
        $staff = Staff::where('id', $validated['staff_id'])
            ->where('tenant_id', $tenantId)
            ->first();
            
        if (!$staff) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['staff_id' => 'Invalid staff member selected.']);
        }

        $this->attendanceService->markAttendance(array_merge($validated, ['tenant_id' => $tenantId]));

        return redirect()->route('attendance.index')->with('success', 'Attendance marked successfully!');
    }

    public function bulkCreate()
    {
        $tenantId = auth()->user()->tenant_id;
        $staffList = $this->staffService->getAllActive($tenantId);
        
        return view('attendance.bulk', compact('staffList'));
    }

    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'attendance' => 'required|array',
            'attendance.*.staff_id' => 'required|exists:staff,id',
            'attendance.*.status' => 'required|in:present,absent,late,half_day',
            'attendance.*.check_in_time' => 'nullable|date_format:H:i',
            'attendance.*.check_out_time' => 'nullable|date_format:H:i',
            'attendance.*.notes' => 'nullable|string|max:500',
        ]);

        $tenantId = auth()->user()->tenant_id;
        
        $attendanceData = [];
        foreach ($validated['attendance'] as $data) {
            $attendanceData[] = [
                'staff_id' => $data['staff_id'],
                'status' => $data['status'],
                'check_in_time' => $data['check_in_time'] ?? null,
                'check_out_time' => $data['check_out_time'] ?? null,
                'notes' => $data['notes'] ?? null,
            ];
        }

        $this->attendanceService->bulkMarkAttendance($tenantId, $validated['date'], $attendanceData);

        return redirect()->route('attendance.index')->with('success', 'Bulk attendance marked successfully!');
    }

    public function edit(Attendance $attendance)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($attendance->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $staffList = $this->staffService->getAllActive($tenantId);

        return view('attendance.edit', compact('attendance', 'staffList'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($attendance->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'date' => 'required|date|before_or_equal:today',
            'status' => 'required|in:present,absent,late,half_day',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i|after:check_in_time',
            'notes' => 'nullable|string|max:500',
        ]);

        $attendance->update($validated);

        return redirect()->route('attendance.index')->with('success', 'Attendance updated successfully!');
    }

    public function report(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $report = $this->attendanceService->getAttendanceReport($tenantId, $startDate, $endDate);

        return view('attendance.report', compact('report', 'startDate', 'endDate'));
    }

    public function staffHistory(Request $request, Staff $staff)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($staff->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $attendances = $this->attendanceService->getByStaff($staff->id, $startDate, $endDate);
        $stats = $this->attendanceService->getStaffAttendanceStats($staff->id, $startDate, $endDate);

        return view('attendance.staff', compact('staff', 'attendances', 'stats', 'startDate', 'endDate'));
    }
}

