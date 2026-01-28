<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Repositories\EquipmentRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

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
    public function getEquipmentCategories(int $tenantId, int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = EquipmentCategory::where('tenant_id', $tenantId);
        
        // Apply name filter
        if (isset($filters['name_like']) && !empty($filters['name_like'])) {
            $query->where('name', 'like', '%' . $filters['name_like'] . '%');
        }
        
        // Apply status filter
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }
        
        // Apply sorting
        if (isset($filters['sort_by']) && !empty($filters['sort_by'])) {
            $sortOrder = $filters['sort_order'] ?? 'asc';
            $query->orderBy($filters['sort_by'], $sortOrder);
        } else {
            $query->orderBy('name', 'asc');
        }
        
        return $query->paginate($perPage)->appends($filters);
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
}

