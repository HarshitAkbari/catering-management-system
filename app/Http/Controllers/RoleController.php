<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::where('tenant_id', auth()->user()->tenant_id)
            ->with('permissions')
            ->get();

        $permissions = Permission::where('tenant_id', auth()->user()->tenant_id)->get();

        return view('roles.index', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,NULL,id,tenant_id,' . auth()->user()->tenant_id,
            'display_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'tenant_id' => auth()->user()->tenant_id,
            'name' => $validated['name'],
            'display_name' => $validated['display_name'] ?? $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        if (isset($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        return redirect()->route('roles.index')->with('success', 'Role created successfully!');
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'display_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'display_name' => $validated['display_name'] ?? $role->display_name,
            'description' => $validated['description'] ?? $role->description,
        ]);

        if (isset($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        return redirect()->route('roles.index')->with('success', 'Role updated successfully!');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully!');
    }

    public function assignRole(Request $request, User $user)
    {
        $roles = Role::where('tenant_id', auth()->user()->tenant_id)->get();
        $userRoles = $user->roles;

        return view('roles.assign', compact('user', 'roles', 'userRoles'));
    }

    public function storeRoleAssignment(Request $request, User $user)
    {
        $validated = $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $user->roles()->sync($validated['roles']);

        return redirect()->route('roles.index')->with('success', 'Roles assigned successfully!');
    }
}

