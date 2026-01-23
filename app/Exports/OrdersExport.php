<?php

declare(strict_types=1);

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrdersExport implements FromQuery, WithHeadings, WithMapping, WithStyles
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
        // $order is now an Order model instance from the query
        // total_amount is added via join in the controller
        return [
            $order->order_number,
            $order->customer->name ?? '-',
            $order->event_date->format('Y-m-d'),
            $order->eventTime->name ?? '-',
            $order->orderType->name ?? '-',
            $order->guest_count ?? '-',
            $order->total_amount ?? $order->estimated_cost ?? 0,
            $order->orderStatus->name ?? '-',
            $order->payment_status ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

