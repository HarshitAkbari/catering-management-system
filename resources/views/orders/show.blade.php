@extends('layouts.app')

@section('title', 'Order Details')

@section('page_content')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Orders</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">Order Details</a></li>
    </ol>
</div>

@php
    $totalAmount = $relatedOrders->sum('estimated_cost');
    $eventCount = $relatedOrders->count();
    $firstOrder = $relatedOrders->first();
@endphp

<div class="row">
    <div class="col-lg-12">
        <!-- Summary Section -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Order Summary</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 col-md-6 mb-3">
                        <p class="mb-0"><span class="text-muted">Order Number :</span> <strong>{{ $order->order_number }}</strong></p>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <p class="mb-0"><span class="text-muted">Customer :</span> <strong>{{ $order->customer->name }}</strong></p>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <p class="mb-0"><span class="text-muted">Contact :</span> <strong>{{ $order->customer->mobile }}</strong></p>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <p class="mb-0"><span class="text-muted">Total Amount :</span> <strong>₹{{ number_format($totalAmount, 2) }}</strong></p>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <p class="mb-0"><span class="text-muted">Number of Events :</span> <strong>{{ $eventCount }}</strong></p>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <p class="mb-0"><span class="text-muted">Address :</span> <strong>{{ $order->address }}</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <!-- Orders Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">All Orders</h4>
                <a href="{{ route('orders.index') }}" class="btn btn-primary btn-sm">Back to Orders</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <thead>
                            <tr>
                                <th class="align-middle">Order Number</th>
                                <th class="align-middle">Event Date</th>
                                <th class="align-middle">Event Time</th>
                                <th class="align-middle">Event Menu</th>
                                <th class="align-middle">Guest Count</th>
                                <th class="align-middle">Order Type</th>
                                <th class="align-middle">Dish Price</th>
                                <th class="align-middle text-end">Cost</th>
                                <th class="align-middle text-end">Status</th>
                                <th class="align-middle text-end">Payment Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($relatedOrders as $relatedOrder)
                                @php
                                    $eventTimeLabels = [
                                        'morning' => 'Morning',
                                        'afternoon' => 'Afternoon',
                                        'evening' => 'Evening',
                                        'night_snack' => 'Snack'
                                    ];
                                    $orderTypeLabels = [
                                        'full_service' => 'Full Service',
                                        'preparation_only' => 'Preparation Only'
                                    ];
                                @endphp
                                <tr class="btn-reveal-trigger">
                                    <td class="py-2">
                                        <strong>{{ $relatedOrder->order_number }}</strong>
                                    </td>
                                    <td class="py-2">
                                        {{ $relatedOrder->event_date ? $relatedOrder->event_date->format('M d, Y') : 'N/A' }}
                                    </td>
                                    <td class="py-2">
                                        {{ $relatedOrder->event_time ? ($eventTimeLabels[$relatedOrder->event_time] ?? ucfirst(str_replace('_', ' ', $relatedOrder->event_time))) : 'N/A' }}
                                    </td>
                                    <td class="py-2">
                                        {{ $relatedOrder->event_menu ?? 'N/A' }}
                                    </td>
                                    <td class="py-2">
                                        {{ $relatedOrder->guest_count ?? 'N/A' }}
                                    </td>
                                    <td class="py-2">
                                        {{ $relatedOrder->order_type ? ($orderTypeLabels[$relatedOrder->order_type] ?? $relatedOrder->order_type) : 'N/A' }}
                                    </td>
                                    <td class="py-2">
                                        @if($relatedOrder->guest_count && $relatedOrder->estimated_cost)
                                            ₹{{ number_format($relatedOrder->estimated_cost / $relatedOrder->guest_count, 2) }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="py-2 text-end">
                                        <strong>₹{{ number_format($relatedOrder->estimated_cost ?? 0, 2) }}</strong>
                                    </td>
                                    <td class="py-2 text-end">
                                        @if($relatedOrder->status === 'confirmed')
                                            <span class="badge badge-success">Confirmed</span>
                                        @elseif($relatedOrder->status === 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($relatedOrder->status === 'completed')
                                            <span class="badge badge-primary">Completed</span>
                                        @elseif($relatedOrder->status === 'cancelled')
                                            <span class="badge badge-danger">Cancelled</span>
                                        @else
                                            <span class="badge badge-secondary">{{ ucfirst($relatedOrder->status ?? 'N/A') }}</span>
                                        @endif
                                    </td>
                                    <td class="py-2 text-end">
                                        @if($relatedOrder->payment_status === 'paid')
                                            <span class="badge badge-success">Paid</span>
                                        @elseif($relatedOrder->payment_status === 'partial')
                                            <span class="badge badge-warning">Partial</span>
                                        @elseif($relatedOrder->payment_status === 'pending')
                                            <span class="badge badge-danger">Pending</span>
                                        @else
                                            <span class="badge badge-secondary">{{ ucfirst($relatedOrder->payment_status ?? 'N/A') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="7" class="text-end">
                                    <strong>Total:</strong>
                                </td>
                                <td class="text-end">
                                    <strong>₹{{ number_format($totalAmount, 2) }}</strong>
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
