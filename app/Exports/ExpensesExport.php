<?php

declare(strict_types=1);

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExpensesExport implements FromQuery, WithHeadings, WithMapping, WithStyles
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
            'Date',
            'Item Name',
            'Quantity',
            'Vendor',
            'Price',
        ];
    }

    public function map($expense): array
    {
        return [
            $expense->created_at->format('Y-m-d'),
            $expense->inventoryItem->name ?? '-',
            $expense->quantity,
            $expense->vendor->name ?? '-',
            $expense->price ?? 0,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

