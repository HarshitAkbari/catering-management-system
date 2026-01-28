<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Models\Staff;
use App\Repositories\StaffRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class StaffService extends BaseService
{
    protected StaffRepository $repository;

    public function __construct(StaffRepository $repository)
    {
        parent::__construct($repository);
        $this->repository = $repository;
    }

    /**
     * Get staff by tenant
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
     * Get all active staff (for dropdowns, etc.)
     */
    public function getAllActive(?int $tenantId = null): Collection
    {
        // If tenantId is not provided, get it from authenticated user
        if ($tenantId === null) {
            $tenantId = auth()->user()?->tenant_id;
            
            if ($tenantId === null) {
                return new Collection();
            }
        }
        
        return $this->repository->getActiveStaff($tenantId);
    }

    /**
     * Get available staff for an event
     */
    public function getAvailableStaff(int $tenantId, string $eventDate, ?int $eventTimeId = null): Collection
    {
        return $this->repository->getAvailableStaff($tenantId, $eventDate, $eventTimeId);
    }

    /**
     * Get staff by ID
     */
    public function getById(int $id, ?array $relations = null): ?Staff
    {
        return $this->repository->find($id, $relations);
    }

    /**
     * Create staff
     */
    public function create(array $data): Staff
    {
        /** @var Staff $staff */
        $staff = $this->repository->create($data);
        return $staff;
    }

    /**
     * Update staff
     */
    public function update(Model $model, array $data): bool|array
    {
        /** @var Staff $model */
        return parent::update($model, $data);
    }

    /**
     * Delete staff
     */
    public function delete(Model $model): bool
    {
        /** @var Staff $model */
        // Check if staff has active event assignments
        $activeAssignments = $model->orders()
            ->where('event_date', '>=', now()->toDateString())
            ->count();

        if ($activeAssignments > 0) {
            return false; // Cannot delete staff with active assignments
        }

        return parent::delete($model);
    }

    /**
     * Assign staff to event
     */
    public function assignToEvent(int $orderId, array $staffData): bool
    {
        $order = Order::find($orderId);
        
        if (!$order) {
            return false;
        }

        $syncData = [];
        foreach ($staffData as $assignment) {
            $staffId = $assignment['staff_id'] ?? null;
            $role = $assignment['role'] ?? '';
            $notes = $assignment['notes'] ?? null;

            if ($staffId) {
                $syncData[$staffId] = [
                    'role' => $role,
                    'notes' => $notes,
                ];
            }
        }

        $order->staff()->sync($syncData);

        return true;
    }

    /**
     * Get staff workload
     */
    public function getStaffWorkload(int $staffId, ?string $startDate = null, ?string $endDate = null): array
    {
        $startDate = $startDate ?? now()->startOfMonth()->toDateString();
        $endDate = $endDate ?? now()->endOfMonth()->toDateString();

        return $this->repository->getStaffWorkload($staffId, $startDate, $endDate);
    }

    /**
     * Get staff performance
     */
    public function getStaffPerformance(int $staffId, ?string $startDate = null, ?string $endDate = null): array
    {
        $startDate = $startDate ?? now()->subMonths(6)->startOfMonth()->toDateString();
        $endDate = $endDate ?? now()->endOfMonth()->toDateString();

        $staff = $this->repository->find($staffId);
        
        if (!$staff) {
            return [];
        }

        $events = $staff->orders()
            ->whereBetween('event_date', [$startDate, $endDate])
            ->get();

        $attendances = $staff->attendances()
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $totalDays = $attendances->count();
        $presentDays = $attendances->where('status', 'present')->count();
        $attendanceRate = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0;

        return [
            'total_events' => $events->count(),
            'attendance_rate' => $attendanceRate,
            'total_attendance_days' => $totalDays,
            'present_days' => $presentDays,
            'events' => $events,
            'attendances' => $attendances,
        ];
    }

    /**
     * Get staff roles (from StaffRole model)
     */
    public function getStaffRoles(int $tenantId): \Illuminate\Database\Eloquent\Collection
    {
        return \App\Models\StaffRole::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Toggle staff status between active and inactive
     */
    public function toggleStatus(Staff $staff): array
    {
        try {
            $newStatus = $staff->status === 'active' ? 'inactive' : 'active';
            
            $this->repository->update($staff, [
                'status' => $newStatus,
            ]);

            return [
                'status' => $newStatus,
                'message' => $newStatus === 'active' 
                    ? 'Staff member activated successfully.' 
                    : 'Staff member deactivated successfully.',
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to toggle staff status: ' . $e->getMessage(),
            ];
        }
    }
}
