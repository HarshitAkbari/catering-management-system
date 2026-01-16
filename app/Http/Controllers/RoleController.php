<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Services\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct(
        private readonly RoleService $roleService
    ) {}

    public function index()
    {
        $tenantId = auth()->user()->tenant_id;
        $roles = $this->roleService->getByTenant($tenantId);
        $permissions = $this->roleService->getPermissionsByTenant($tenantId);

        return view('roles.index', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,NULL,id,tenant_id,' . $tenantId,
            'display_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $result = $this->roleService->createRole($validated, $tenantId);

        if (!$result['status']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $result['message']]);
        }

        return redirect()->route('roles.index')->with('success', 'Role created successfully!');
    }

    public function update(Request $request, Role $role)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($role->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'display_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $result = $this->roleService->updateRole($role, $validated, $tenantId);

        if (!$result['status']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $result['message']]);
        }

        return redirect()->route('roles.index')->with('success', 'Role updated successfully!');
    }

    public function destroy(Role $role)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($role->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $this->roleService->delete($role);
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully!');
    }

    public function assignRole(Request $request, User $user)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($user->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $roles = $this->roleService->getByTenant($tenantId);
        $userRoles = $user->roles;

        return view('roles.assign', compact('user', 'roles', 'userRoles'));
    }

    public function storeRoleAssignment(Request $request, User $user)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($user->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $result = $this->roleService->assignRolesToUser($user, $validated['roles'], $tenantId);

        if (!$result['status']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $result['message']]);
        }

        return redirect()->route('roles.index')->with('success', 'Roles assigned successfully!');
    }
}
