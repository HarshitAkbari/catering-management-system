<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users for the current tenant.
     */
    public function index()
    {
        $users = User::where('tenant_id', auth()->user()->tenant_id)
            ->with('roles')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->where(function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                }),
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,manager,staff'],
            'status' => ['required', 'in:active,inactive'],
            'role_ids' => ['nullable', 'array'],
            'role_ids.*' => ['exists:roles,id'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'tenant_id' => $tenantId,
            'role' => $validated['role'],
            'status' => $validated['status'],
        ]);

        // Sync roles if provided
        if (isset($validated['role_ids'])) {
            // Ensure role_ids belong to the same tenant
            $validRoleIds = Role::where('tenant_id', $tenantId)
                ->whereIn('id', $validated['role_ids'])
                ->pluck('id')
                ->toArray();
            
            $user->roles()->sync($validRoleIds);
        } else {
            // Sync enum role with Role model
            $user->syncRoleModel();
        }

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        // Ensure user belongs to the same tenant
        if ($user->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'Unauthorized');
        }

        $roles = Role::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        // Ensure user belongs to the same tenant
        if ($user->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'Unauthorized');
        }

        $tenantId = auth()->user()->tenant_id;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->where(function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                })->ignore($user->id),
            ],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,manager,staff'],
            'status' => ['required', 'in:active,inactive'],
            'role_ids' => ['nullable', 'array'],
            'role_ids.*' => ['exists:roles,id'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->status = $validated['status'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Sync roles if provided
        if (isset($validated['role_ids'])) {
            // Ensure role_ids belong to the same tenant
            $validRoleIds = Role::where('tenant_id', $tenantId)
                ->whereIn('id', $validated['role_ids'])
                ->pluck('id')
                ->toArray();
            
            $user->roles()->sync($validRoleIds);
        } else {
            // Sync enum role with Role model
            $user->syncRoleModel();
        }

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        // Ensure user belongs to the same tenant
        if ($user->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'Unauthorized');
        }

        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}

