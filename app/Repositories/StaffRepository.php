<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Staff;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class StaffRepository extends BaseRepository
{
    protected array $searchable = ['name', 'phone', 'email', 'staff_role'];

    public function __construct(Staff $model)
    {
        parent::__construct($model);
    }

    /**
     * Get staff by tenant ID
     */
    public function getByTenant(int $tenantId, array $filters = []): Collection
    {
        $query = $this->model->where('tenant_id', $tenantId);

        // Apply filters
        if (isset($filters['staff_role']) && !empty($filters['staff_role'])) {
            $query->where('staff_role', $filters['staff_role']);
        }

        if (isset($filters['status']) && !empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['name_like']) && !empty($filters['name_like'])) {
            $query->where('name', 'like', '%' . $filters['name_like'] . '%');
        }

        if (isset($filters['phone_like']) && !empty($filters['phone_like'])) {
            $query->where('phone', 'like', '%' . $filters['phone_like'] . '%');
        }

        if (isset($filters['email_like']) && !empty($filters['email_like'])) {
            $query->where('email', 'like', '%' . $filters['email_like'] . '%');
        }

        return $query->orderBy('name', 'asc')->get();
    }

    /**
     * Get active staff
     */
    public function getActiveStaff(int $tenantId): Collection
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Get available staff (not assigned to conflicting events)
     */
    public function getAvailableStaff(int $tenantId, string $eventDate, ?int $eventTimeId = null): Collection
    {
        $date = Carbon::parse($eventDate)->toDateString();

        // Get all active staff
        $allStaff = $this->getActiveStaff($tenantId);

        // Get staff already assigned to events on the same date
        $busyStaffIds = $this->model
            ->where('tenant_id', $tenantId)
            ->whereHas('orders', function ($query) use ($date, $eventTimeId) {
                $query->where('event_date', $date);
                if ($eventTimeId) {
                    $query->where('event_time_id', $eventTimeId);
                }
            })
            ->pluck('id')
            ->toArray();

        // Filter out busy staff
        return $allStaff->reject(function ($staff) use ($busyStaffIds) {
            return in_array($staff->id, $busyStaffIds);
        })->values();
    }

    /**
     * Get staff by role
     */
    public function getStaffByRole(int $tenantId, string $role): Collection
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->where('staff_role', $role)
            ->where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Get staff workload
     */
    public function getStaffWorkload(int $staffId, string $startDate, string $endDate): array
    {
        $staff = $this->model->find($staffId);

        if (!$staff) {
            return [];
        }

        $events = $staff->orders()
            ->whereBetween('event_date', [$startDate, $endDate])
            ->get();

        return [
            'total_events' => $events->count(),
            'events' => $events,
            'events_per_week' => $events->count() > 0 ? round($events->count() / max(1, Carbon::parse($startDate)->diffInWeeks(Carbon::parse($endDate))), 2) : 0,
        ];
    }
}
