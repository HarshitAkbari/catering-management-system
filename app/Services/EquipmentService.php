<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\EquipmentStatus;
use App\Repositories\EquipmentRepository;
use Illuminate\Database\Eloquent\Collection;

class EquipmentService extends BaseService
{
    protected EquipmentRepository $repository;

    public function __construct(EquipmentRepository $repository)
    {
        parent::__construct($repository);
        $this->repository = $repository;
    }

    /**
     * Get equipment by tenant
     */
    public function getByTenant(int $tenantId, int $perPage = 15, array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
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
     * Get all equipment (for dropdowns, etc.)
     */
    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    /**
     * Get available equipment
     */
    public function getAvailable(int $tenantId): Collection
    {
        return $this->repository->getAvailable($tenantId);
    }

    /**
     * Get equipment by ID
     */
    public function getById(int $id, ?array $relations = null): ?Equipment
    {
        return $this->repository->find($id, $relations);
    }

    // Equipment Categories Methods
    public function getEquipmentCategories(int $tenantId): Collection
    {
        return EquipmentCategory::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();
    }

    public function createEquipmentCategory(array $data, int $tenantId): array
    {
        try {
            $data['tenant_id'] = $tenantId;
            $data['is_active'] = $data['is_active'] ?? true;

            EquipmentCategory::create($data);

            return ['status' => true];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to create equipment category: ' . $e->getMessage(),
            ];
        }
    }

    public function updateEquipmentCategory(EquipmentCategory $equipmentCategory, array $data): array
    {
        try {
            $equipmentCategory->update($data);

            return ['status' => true];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to update equipment category: ' . $e->getMessage(),
            ];
        }
    }

    public function deleteEquipmentCategory(EquipmentCategory $equipmentCategory): array
    {
        try {
            // Check if category is used in equipment
            $equipmentCount = Equipment::where('equipment_category_id', $equipmentCategory->id)->count();

            if ($equipmentCount > 0) {
                return [
                    'status' => false,
                    'message' => "Cannot delete equipment category. It is being used by {$equipmentCount} equipment item(s).",
                ];
            }

            $equipmentCategory->delete();

            return ['status' => true];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to delete equipment category: ' . $e->getMessage(),
            ];
        }
    }

    public function toggleEquipmentCategory(EquipmentCategory $equipmentCategory): array
    {
        try {
            $equipmentCategory->update(['is_active' => !$equipmentCategory->is_active]);

            return [
                'status' => true,
                'is_active' => $equipmentCategory->is_active,
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to toggle equipment category: ' . $e->getMessage(),
            ];
        }
    }

    // Equipment Statuses Methods
    public function getEquipmentStatuses(int $tenantId): Collection
    {
        return EquipmentStatus::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();
    }

    public function createEquipmentStatus(array $data, int $tenantId): array
    {
        try {
            $data['tenant_id'] = $tenantId;
            $data['is_active'] = $data['is_active'] ?? true;

            EquipmentStatus::create($data);

            return ['status' => true];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to create equipment status: ' . $e->getMessage(),
            ];
        }
    }

    public function updateEquipmentStatus(EquipmentStatus $equipmentStatus, array $data): array
    {
        try {
            $equipmentStatus->update($data);

            return ['status' => true];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to update equipment status: ' . $e->getMessage(),
            ];
        }
    }

    public function deleteEquipmentStatus(EquipmentStatus $equipmentStatus): array
    {
        try {
            // Check if status is used in equipment
            $equipmentCount = Equipment::where('equipment_status_id', $equipmentStatus->id)->count();

            if ($equipmentCount > 0) {
                return [
                    'status' => false,
                    'message' => "Cannot delete equipment status. It is being used by {$equipmentCount} equipment item(s).",
                ];
            }

            $equipmentStatus->delete();

            return ['status' => true];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to delete equipment status: ' . $e->getMessage(),
            ];
        }
    }

    public function toggleEquipmentStatus(EquipmentStatus $equipmentStatus): array
    {
        try {
            $equipmentStatus->update(['is_active' => !$equipmentStatus->is_active]);

            return [
                'status' => true,
                'is_active' => $equipmentStatus->is_active,
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to toggle equipment status: ' . $e->getMessage(),
            ];
        }
    }
}

