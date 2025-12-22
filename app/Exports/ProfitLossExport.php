<?php

declare(strict_types=1);

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProfitLossExport implements FromArray, WithHeadings, WithStyles
{
    protected float $revenue;
    protected float $expenses;
    protected float $profit;
    protected string $startDate;
    protected string $endDate;

    public function __construct(float $revenue, float $expenses, float $profit, string $startDate, string $endDate)
    {
        $this->revenue = $revenue;
        $this->expenses = $expenses;
        $this->profit = $profit;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function array(): array
    {
        return [
            [
                'Period',
                $this->startDate . ' to ' . $this->endDate,
            ],
            [
                'Revenue',
                $this->revenue,
            ],
            [
                'Expenses',
                $this->expenses,
            ],
            [
                'Profit/Loss',
                $this->profit,
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'Item',
            'Amount (₹)',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
            2 => ['font' => ['bold' => true]],
            3 => ['font' => ['bold' => true]],
            4 => ['font' => ['bold' => true]],
        ];
    }
}

