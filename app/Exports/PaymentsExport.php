<?php

declare(strict_types=1);

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentsExport implements FromQuery, WithHeadings, WithMapping, WithStyles
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
            'Payment Date',
            'Order Number',
            'Amount',
            'Payment Mode',
            'Reference Number',
            'Notes',
        ];
    }

    public function map($payment): array
    {
        return [
            $payment->payment_date->format('Y-m-d'),
            $payment->invoice->order->order_number ?? '-',
            $payment->amount,
            ucfirst(str_replace('_', ' ', $payment->payment_mode)),
            $payment->reference_number ?? '-',
            $payment->notes ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

