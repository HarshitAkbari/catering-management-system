<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserService extends BaseService
{
    protected UserRepository $repository;
    protected RoleRepository $roleRepository;

    public function __construct(UserRepository $repository, RoleRepository $roleRepository)
    {
        parent::__construct($repository);
        $this->repository = $repository;
        $this->roleRepository = $roleRepository;
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
            ['roles'],
            [],
            $perPage
        );
    }

    /**
     * Create user
     */
    public function createUser(array $data, int $tenantId): array
    {
        try {
            return DB::transaction(function () use ($data, $tenantId) {
                $status = $data['status'] ?? 'active';
                $isActive = $status === 'active';
                
                $user = $this->repository->create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'tenant_id' => $tenantId,
                    'role' => $data['role'],
                    'status' => $status,
                    'is_active' => $isActive,
                ]);

                // Sync roles if provided
                if (isset($data['role_ids'])) {
                    $validRoleIds = $this->roleRepository->filter([
                        'tenant_id' => $tenantId,
                    ], [], [], true)->whereIn('id', $data['role_ids'])->pluck('id')->toArray();
                    
                    $user->roles()->sync($validRoleIds);
                } else {
                    $user->syncRoleModel();
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
                $updateData = [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'role' => $data['role'],
                ];

                if (!empty($data['password'])) {
                    $updateData['password'] = Hash::make($data['password']);
                }

                $this->repository->update($user, $updateData);

                // Sync roles if provided
                if (isset($data['role_ids'])) {
                    $validRoleIds = $this->roleRepository->filter([
                        'tenant_id' => $tenantId,
                    ], [], [], true)->whereIn('id', $data['role_ids'])->pluck('id')->toArray();
                    
                    $user->roles()->sync($validRoleIds);
                } else {
                    $user->syncRoleModel();
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
            $this->repository->delete($user);
            return ['status' => true, 'message' => 'User deleted successfully.'];
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
}

