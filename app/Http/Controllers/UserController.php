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
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        
        // Build filters from request
        $filters = ['tenant_id' => $tenantId];
        
        // Name filter
        if ($request->has('name_like') && !empty($request->name_like)) {
            $filters['name_like'] = $request->name_like;
        }
        
        // Email filter
        if ($request->has('email_like') && !empty($request->email_like)) {
            $filters['email_like'] = $request->email_like;
        }
        
        // Role filter
        if ($request->has('role') && !empty($request->role)) {
            $filters['role'] = $request->role;
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
        
        $users = $this->userService->getByTenant($tenantId, 15, $filters);
        
        // Pass filter values to view for form preservation
        $filterValues = [
            'name_like' => $request->input('name_like', ''),
            'email_like' => $request->input('email_like', ''),
            'role' => $request->input('role', ''),
            'status' => $request->input('status', ''),
        ];
        
        $page_title = 'Users';
        $subtitle = 'Manage system users and permissions';
        
        return view('users.index', compact('users', 'filterValues', 'page_title', 'subtitle'));
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
