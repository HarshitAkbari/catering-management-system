<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {}

    /**
     * Display a listing of users for the current tenant.
     */
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;
        $users = $this->userService->getByTenant($tenantId);

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $tenantId = auth()->user()->tenant_id;
        $roles = $this->userService->getRolesForTenant($tenantId);

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

        $result = $this->userService->createUser($validated, $tenantId);

        if (!$result['status']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $result['message']]);
        }

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($user->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $roles = $this->userService->getRolesForTenant($tenantId);

        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($user->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

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

        $result = $this->userService->updateUser($user, $validated, $tenantId);

        if (!$result['status']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $result['message']]);
        }

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($user->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $result = $this->userService->deleteUser($user, auth()->id());

        if (!$result['status']) {
            return redirect()->route('users.index')
                ->with('error', $result['message']);
        }

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}
