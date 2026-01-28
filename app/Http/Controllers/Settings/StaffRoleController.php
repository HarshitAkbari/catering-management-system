<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\StoreStaffRoleRequest;
use App\Http\Requests\Settings\UpdateStaffRoleRequest;
use App\Models\StaffRole;
use App\Services\SettingsService;
use Illuminate\Http\Request;

class StaffRoleController extends Controller
{
    public function __construct(
        private readonly SettingsService $settingsService
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
        
        $staffRoles = $this->settingsService->getStaffRoles($tenantId, 15, $filters);
        
        // Pass filter values to view for form preservation
        $filterValues = [
            'name_like' => $request->input('name_like', ''),
            'status' => $request->input('status', ''),
        ];
        
        $page_title = 'Staff Roles';
        return view('settings.staff_roles.index', compact('staffRoles', 'filterValues', 'page_title'));
    }

    public function create()
    {
        $page_title = 'Create Staff Role';
        return view('settings.staff_roles.create', compact('page_title'));
    }

    public function store(StoreStaffRoleRequest $request)
    {
        $validated = $request->validated();
        $tenantId = auth()->user()->tenant_id;
        
        $result = $this->settingsService->createStaffRole($validated, $tenantId);

        if (!$result['status']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $result['message']]);
        }

        return redirect()->route('settings.staff-roles')
            ->with('success', 'Staff role created successfully!');
    }

    public function edit(StaffRole $staffRole)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($staffRole->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $page_title = 'Edit Staff Role';
        return view('settings.staff_roles.edit', compact('staffRole', 'page_title'));
    }

    public function update(UpdateStaffRoleRequest $request, StaffRole $staffRole)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($staffRole->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validated();
        $result = $this->settingsService->updateStaffRole($staffRole, $validated);

        if (!$result['status']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $result['message']]);
        }

        return redirect()->route('settings.staff-roles')
            ->with('success', 'Staff role updated successfully!');
    }

    public function destroy(StaffRole $staffRole)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($staffRole->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $result = $this->settingsService->deleteStaffRole($staffRole);

        if (!$result['status']) {
            return redirect()->route('settings.staff-roles')
                ->with('error', $result['message']);
        }

        return redirect()->route('settings.staff-roles')
            ->with('success', 'Staff role deleted successfully!');
    }

    public function toggle(StaffRole $staffRole)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($staffRole->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $result = $this->settingsService->toggleStaffRole($staffRole);

        if (!$result['status']) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], 422);
            }

            return redirect()->route('settings.staff-roles')
                ->with('error', $result['message']);
        }

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $result['is_active'],
                'message' => $result['is_active'] 
                    ? 'Staff role activated successfully!' 
                    : 'Staff role deactivated successfully!',
            ]);
        }

        return redirect()->route('settings.staff-roles')
            ->with('success', $result['is_active'] 
                ? 'Staff role activated successfully!' 
                : 'Staff role deactivated successfully!');
    }
}
