<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Attendance;
use App\Repositories\AttendanceRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AttendanceService extends BaseService
{
    protected AttendanceRepository $repository;

    public function __construct(AttendanceRepository $repository)
    {
        parent::__construct($repository);
        $this->repository = $repository;
    }

    /**
     * Get attendance by tenant
     */
    public function getByTenant(int $tenantId, int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        // Merge tenant_id filter if not already present
        if (!isset($filters['tenant_id'])) {
            $filters['tenant_id'] = $tenantId;
        }
        
        // Convert date_from and date_to to date_between format for BaseRepository
        if (isset($filters['date_from']) && isset($filters['date_to'])) {
            $filters['date_between'] = [
                'from' => $filters['date_from'],
                'to' => $filters['date_to'],
            ];
            unset($filters['date_from'], $filters['date_to']);
        }
        
        return $this->repository->filterAndPaginate(
            $filters,
            ['staff'],
            [],
            $perPage
        );
    }

    /**
     * Mark attendance
     */
    public function markAttendance(array $data): Attendance
    {
        // Check if attendance already exists for this staff on this date
        $existing = Attendance::where('tenant_id', $data['tenant_id'])
            ->where('staff_id', $data['staff_id'])
            ->where('date', $data['date'])
            ->first();

        if ($existing) {
            // Update existing record
            $existing->update($data);
            return $existing;
        }

        return $this->repository->create($data);
    }

    /**
     * Bulk mark attendance
     */
    public function bulkMarkAttendance(int $tenantId, string $date, array $attendanceData): bool
    {
        try {
            foreach ($attendanceData as $data) {
                $data['tenant_id'] = $tenantId;
                $data['date'] = $date;
                
                $this->markAttendance($data);
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get attendance report
     */
    public function getAttendanceReport(int $tenantId, string $startDate, string $endDate): array
    {
        $attendances = Attendance::where('tenant_id', $tenantId)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('staff')
            ->get();

        $totalRecords = $attendances->count();
        $present = $attendances->where('status', 'present')->count();
        $absent = $attendances->where('status', 'absent')->count();
        $late = $attendances->where('status', 'late')->count();
        $halfDay = $attendances->where('status', 'half_day')->count();

        $attendanceRate = $totalRecords > 0 ? round(($present / $totalRecords) * 100, 2) : 0;

        // Group by date
        $byDate = $attendances->groupBy(function ($attendance) {
            return $attendance->date->format('Y-m-d');
        });

        // Group by staff
        $byStaff = $attendances->groupBy('staff_id');

        return [
            'total_records' => $totalRecords,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'half_day' => $halfDay,
            'attendance_rate' => $attendanceRate,
            'by_date' => $byDate,
            'by_staff' => $byStaff,
            'attendances' => $attendances,
        ];
    }

    /**
     * Get staff attendance statistics
     */
    public function getStaffAttendanceStats(int $staffId, string $startDate, string $endDate): array
    {
        return $this->repository->getAttendanceStats($staffId, $startDate, $endDate);
    }

    /**
     * Get attendance by date
     */
    public function getByDate(int $tenantId, string $date): Collection
    {
        return $this->repository->getByDate($tenantId, $date);
    }

    /**
     * Get attendance by staff
     */
    public function getByStaff(int $staffId, string $startDate, string $endDate): Collection
    {
        return $this->repository->getByStaff($staffId, $startDate, $endDate);
    }
}

