<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Collection;

class AttendanceRepository extends BaseRepository
{
    protected array $searchable = ['notes'];

    public function __construct(Attendance $model)
    {
        parent::__construct($model);
    }

    /**
     * Get attendance by staff ID
     */
    public function getByStaff(int $staffId, string $startDate, string $endDate): Collection
    {
        return $this->model
            ->where('staff_id', $staffId)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();
    }

    /**
     * Get attendance by date
     */
    public function getByDate(int $tenantId, string $date): Collection
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->where('date', $date)
            ->with('staff')
            ->orderBy('staff_id', 'asc')
            ->get();
    }

    /**
     * Get attendance statistics
     */
    public function getAttendanceStats(int $staffId, string $startDate, string $endDate): array
    {
        $attendances = $this->getByStaff($staffId, $startDate, $endDate);

        $total = $attendances->count();
        $present = $attendances->where('status', 'present')->count();
        $absent = $attendances->where('status', 'absent')->count();
        $late = $attendances->where('status', 'late')->count();
        $halfDay = $attendances->where('status', 'half_day')->count();

        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'half_day' => $halfDay,
            'attendance_rate' => $total > 0 ? round(($present / $total) * 100, 2) : 0,
        ];
    }
}

