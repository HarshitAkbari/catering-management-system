<?php

declare(strict_types=1);

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    protected Builder $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function query(): Builder
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'Staff Name',
            'Date',
            'Status',
            'Notes',
        ];
    }

    public function map($attendance): array
    {
        $statusLabels = [
            'present' => 'Present',
            'absent' => 'Absent',
            'half_day' => 'Half Day',
        ];

        return [
            $attendance->staff->name ?? '-',
            $attendance->date->format('Y-m-d'),
            $statusLabels[$attendance->status] ?? ucfirst($attendance->status),
            $attendance->notes ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

