<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Staff;
use App\Models\User;
use App\Notifications\UserCreatedNotification;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Services\StaffService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserService extends BaseService
{
    protected UserRepository $repository;
    protected RoleRepository $roleRepository;
    protected StaffService $staffService;

    public function __construct(UserRepository $repository, RoleRepository $roleRepository, StaffService $staffService)
    {
        parent::__construct($repository);
        $this->repository = $repository;
        $this->roleRepository = $roleRepository;
        $this->staffService = $staffService;
    }

    /**
     * Get users by tenant
     */
    public function getByTenant(int $tenantId, int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        // Merge tenant_id filter if not already present
        if (!isset($filters['tenant_id'])) {
            $filters['tenant_id'] = $tenantId;
        }
        
        return $this->repository->filterAndPaginate(
            $filters,
            [],
            [],
            $perPage
        );
    }

    /**
     * Create user
     */
    public function createUser(array $data, int $tenantId, string $temporaryPassword): array
    {
        try {
            return DB::transaction(function () use ($data, $tenantId, $temporaryPassword) {
                $status = $data['status'] ?? 'active';
                $isActive = $status === 'active';
                
                $user = $this->repository->create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'mobile' => $data['mobile'] ?? null,
                    'address' => $data['address'] ?? null,
                    'password' => Hash::make($temporaryPassword),
                    'tenant_id' => $tenantId,
                    'role' => $data['role'],
                    'status' => $status,
                    'is_active' => $isActive,
                ]);

                // Sync primary role
                $user->syncRoleModel();

                // Create staff entry if role is staff or manager
                if (in_array($data['role'], ['staff', 'manager']) && !empty($data['mobile'])) {
                    $this->createStaffEntry($user, $data);
                }

                // Send notification with temporary password for all roles
                try {
                    $user->notify(new UserCreatedNotification($temporaryPassword));
                    \Log::info('User creation email sent successfully', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'role' => $user->role,
                    ]);
                } catch (\Exception $e) {
                    // Log the error but don't fail user creation
                    \Log::error('Failed to send user creation email', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'role' => $user->role,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }

                return [
                    'status' => true,
                    'message' => 'User created successfully.',
                    'user' => $user,
                ];
            });
        } catch (\Exception $e) {
            return ['status' => false, 'message' => 'Failed to create user: ' . $e->getMessage()];
        }
    }

    /**
     * Update user
     */
    public function updateUser(User $user, array $data, int $tenantId): array
    {
        if ($user->tenant_id !== $tenantId) {
            return ['status' => false, 'message' => 'Unauthorized'];
        }

        try {
            return DB::transaction(function () use ($user, $data, $tenantId) {
                $oldRole = $user->role;
                
                $updateData = [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'mobile' => $data['mobile'] ?? null,
                    'address' => $data['address'] ?? null,
                    'role' => $data['role'],
                ];

                if (!empty($data['password'])) {
                    $updateData['password'] = Hash::make($data['password']);
                }

                $this->repository->update($user, $updateData);

                // Sync primary role
                $user->syncRoleModel();

                // Handle staff entry creation/update
                if (in_array($data['role'], ['staff', 'manager'])) {
                    if (!empty($data['mobile'])) {
                        $this->updateOrCreateStaffEntry($user, $data);
                    }
                } elseif (in_array($oldRole, ['staff', 'manager']) && $data['role'] === 'admin') {
                    // If role changed from staff/manager to admin, delete staff entry
                    $this->deleteStaffEntry($user);
                }

                return [
                    'status' => true,
                    'message' => 'User updated successfully.',
                ];
            });
        } catch (\Exception $e) {
            return ['status' => false, 'message' => 'Failed to update user: ' . $e->getMessage()];
        }
    }

    /**
     * Delete user
     */
    public function deleteUser(User $user, int $currentUserId): array
    {
        if ($user->id === $currentUserId) {
            return ['status' => false, 'message' => 'You cannot delete your own account.'];
        }

        try {
            return DB::transaction(function () use ($user) {
                // Delete staff entry if exists
                if (in_array($user->role, ['staff', 'manager'])) {
                    $this->deleteStaffEntry($user);
                }
                
                $this->repository->delete($user);
                return ['status' => true, 'message' => 'User deleted successfully.'];
            });
        } catch (\Exception $e) {
            return ['status' => false, 'message' => 'Failed to delete user: ' . $e->getMessage()];
        }
    }

    /**
     * Get roles for tenant
     */
    public function getRolesForTenant(int $tenantId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->roleRepository->filter(['tenant_id' => $tenantId], [], [], true)->orderBy('name')->get();
    }

    /**
     * Toggle user status between active and inactive
     */
    public function toggleUserStatus(User $user): array
    {
        try {
            $newIsActive = !$user->is_active;
            $newStatus = $newIsActive ? 'active' : 'inactive';
            
            $this->repository->update($user, [
                'is_active' => $newIsActive,
                'status' => $newStatus, // Keep status enum in sync for backward compatibility
            ]);

            // Update staff entry status if exists
            if (in_array($user->role, ['staff', 'manager'])) {
                $this->updateStaffStatus($user, $newStatus);
            }

            return [
                'status' => $newStatus,
                'is_active' => $newIsActive,
                'message' => $newIsActive 
                    ? 'User activated successfully.' 
                    : 'User deactivated successfully.',
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to toggle user status: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Create staff entry for user
     */
    protected function createStaffEntry(User $user, array $data): void
    {
        try {
            // Get staff role name from staff_role_id if provided
            $staffRoleName = ucfirst($user->role); // Default to "Staff" or "Manager"
            if (!empty($data['staff_role_id'])) {
                $staffRole = \App\Models\StaffRole::find($data['staff_role_id']);
                if ($staffRole && $staffRole->tenant_id === $user->tenant_id) {
                    $staffRoleName = $staffRole->name;
                }
            }

            $staffData = [
                'tenant_id' => $user->tenant_id,
                'name' => $user->name,
                'phone' => $data['mobile'],
                'email' => $user->email,
                'address' => $data['address'] ?? null,
                'staff_role' => $staffRoleName,
                'staff_role_id' => $data['staff_role_id'] ?? null,
                'status' => $user->status,
            ];

            $this->staffService->create($staffData);
        } catch (\Exception $e) {
            \Log::error('Failed to create staff entry for user: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'role' => $user->role,
            ]);
        }
    }

    /**
     * Update or create staff entry for user
     */
    protected function updateOrCreateStaffEntry(User $user, array $data): void
    {
        try {
            // Try to find existing staff entry by phone/mobile
            $staff = Staff::where('tenant_id', $user->tenant_id)
                ->where('phone', $data['mobile'])
                ->first();

            // Get staff role name from staff_role_id if provided
            $staffRoleName = ucfirst($user->role); // Default to "Staff" or "Manager"
            if (!empty($data['staff_role_id'])) {
                $staffRole = \App\Models\StaffRole::find($data['staff_role_id']);
                if ($staffRole && $staffRole->tenant_id === $user->tenant_id) {
                    $staffRoleName = $staffRole->name;
                }
            }

            $staffData = [
                'name' => $user->name,
                'phone' => $data['mobile'],
                'email' => $user->email,
                'address' => $data['address'] ?? null,
                'staff_role' => $staffRoleName,
                'staff_role_id' => $data['staff_role_id'] ?? null,
                'status' => $user->status,
            ];

            if ($staff) {
                $this->staffService->update($staff, $staffData);
            } else {
                // Create new staff entry
                $staffData['tenant_id'] = $user->tenant_id;
                $this->staffService->create($staffData);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to update/create staff entry for user: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'role' => $user->role,
            ]);
        }
    }

    /**
     * Delete staff entry for user
     */
    protected function deleteStaffEntry(User $user): void
    {
        try {
            if ($user->mobile) {
                $staff = Staff::where('tenant_id', $user->tenant_id)
                    ->where('phone', $user->mobile)
                    ->first();

                if ($staff) {
                    $this->staffService->delete($staff);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to delete staff entry for user: ' . $e->getMessage(), [
                'user_id' => $user->id,
            ]);
        }
    }

    /**
     * Update staff entry status
     */
    protected function updateStaffStatus(User $user, string $status): void
    {
        try {
            if ($user->mobile) {
                $staff = Staff::where('tenant_id', $user->tenant_id)
                    ->where('phone', $user->mobile)
                    ->first();

                if ($staff) {
                    $this->staffService->update($staff, ['status' => $status]);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to update staff status for user: ' . $e->getMessage(), [
                'user_id' => $user->id,
            ]);
        }
    }
}

