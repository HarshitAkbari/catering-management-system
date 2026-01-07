<?php

declare(strict_types=1);

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrdersExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected Collection $orders;

    public function __construct(Collection $orders)
    {
        $this->orders = $orders;
    }

    public function collection(): Collection
    {
        return $this->orders;
    }

    public function headings(): array
    {
        return [
            'Order Number',
            'Customer Name',
            'Event Date',
            'Event Time',
            'Order Type',
            'Guest Count',
            'Total Amount',
            'Status',
            'Payment Status',
        ];
    }

    public function map($order): array
    {
        return [
            $order['order_number'],
            $order['customer']->name ?? '-',
            $order['event_date']->format('Y-m-d'),
            $order['orders']->first()->event_time ?? '-',
            $order['orders']->first()->order_type ?? '-',
            $order['orders']->first()->guest_count ?? '-',
            $order['total_amount'],
            $order['status'],
            $order['payment_status'],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

