@extends('layouts.app')

@section('title', 'Dashboard')

@section('page_content')
<!-- Stat Cards Row -->
<div class="row">
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="widget-stat card bg-primary">
            <div class="card-body p-4">
                <div class="media ai-icon">
                    <span class="me-3 bgl-primary text-primary">
                        <i class="flaticon-381-user-7"></i>
                    </span>
                    <div class="media-body">
                        <p class="mb-1 text-white fw-bold">Total Orders</p>
                        <h4 class="mb-0 text-white fw-bold">{{ $stats['total_orders'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="widget-stat card bg-info">
            <div class="card-body p-4">
                <div class="media ai-icon">
                    <span class="me-3 bgl-info text-info">
                        <i class="flaticon-381-calendar-1"></i>
                    </span>
                    <div class="media-body">
                        <p class="mb-1 text-white fw-bold">Upcoming Events</p>
                        <h4 class="mb-0 text-white fw-bold">{{ $stats['upcoming_events'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="widget-stat card bg-warning">
            <div class="card-body p-4">
                <div class="media ai-icon">
                    <span class="me-3 bgl-warning text-warning">
                        <i class="flaticon-381-settings-2"></i>
                    </span>
                    <div class="media-body">
                        <p class="mb-1 text-white fw-bold">Pending Payments</p>
                        <h4 class="mb-0 text-white fw-bold">{{ $stats['pending_payments'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="widget-stat card bg-success">
            <div class="card-body p-4">
                <div class="media ai-icon">
                    <span class="me-3 bgl-success text-success">
                        <i class="flaticon-381-diamond"></i>
                    </span>
                    <div class="media-body">
                        <p class="mb-1 text-white fw-bold">Completed Events</p>
                        <h4 class="mb-0 text-white fw-bold">{{ $stats['completed_events'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="widget-stat card bg-secondary">
            <div class="card-body p-4">
                <div class="media ai-icon">
                    <span class="me-3 bgl-secondary text-secondary">
                        <i class="flaticon-381-user-7"></i>
                    </span>
                    <div class="media-body">
                        <p class="mb-1 text-white fw-bold">Total Customers</p>
                        <h4 class="mb-0 text-white fw-bold">{{ $stats['total_customers'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="widget-stat card bg-success">
            <div class="card-body p-4">
                <div class="media ai-icon">
                    <span class="me-3 bgl-success text-success">
                        <i class="flaticon-381-heart"></i>
                    </span>
                    <div class="media-body">
                        <p class="mb-1 text-white fw-bold">This Month Revenue</p>
                        <h4 class="mb-0 text-white fw-bold">₹{{ number_format($stats['this_month_revenue'], 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="widget-stat card bg-danger">
            <div class="card-body p-4">
                <div class="media ai-icon">
                    <span class="me-3 bgl-danger text-danger">
                        <i class="flaticon-381-settings-2"></i>
                    </span>
                    <div class="media-body">
                        <p class="mb-1 text-white fw-bold">Low Stock Items</p>
                        <h4 class="mb-0 text-white fw-bold">{{ $lowStockItemsCount }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mt-4">
    <div class="col-xl-4 col-lg-6 col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Revenue Trend</h4>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-6 col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Orders Over Time</h4>
            </div>
            <div class="card-body">
                <canvas id="ordersChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-6 col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Payment Status</h4>
            </div>
            <div class="card-body">
                <canvas id="paymentStatusChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Lists & Alerts Row -->
<div class="row mt-4">
    <!-- Low Stock Alerts -->
    <div class="col-xl-6 col-lg-6 col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Low Stock Alerts</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Current</th>
                                <th>Minimum</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowStockItems as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->name }}</strong><br>
                                        <small class="text-muted">{{ $item->inventoryUnit?->name ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <strong class="text-danger">{{ number_format($item->current_stock, 2) }}</strong>
                                    </td>
                                    <td>
                                        <span>{{ number_format($item->minimum_stock, 2) }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('inventory.stock-in') }}?item={{ $item->id }}" class="btn btn-success btn-xs">
                                            <i class="flaticon-381-add-1"></i> Add Stock
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3 text-muted">No low stock items</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($lowStockItems->count() > 0)
                    <div class="text-center mt-3">
                        <a href="{{ route('inventory.low-stock') }}" class="btn btn-primary btn-sm">View All Low Stock</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Pending Payments -->
    <div class="col-xl-6 col-lg-6 col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Pending Payments</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingPayments as $payment)
                                <tr>
                                    <td>
                                        <a href="{{ route('customers.show', $payment->customer) }}">
                                            {{ $payment->customer->name ?? 'N/A' }}
                                        </a>
                                    </td>
                                    <td>
                                        <strong>₹{{ number_format($payment->estimated_cost, 2) }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $payment->payment_status === 'partial' ? 'warning' : 'danger' }} light">
                                            {{ ucfirst($payment->payment_status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-3 text-muted">No pending payments</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($pendingPayments->count() > 0)
                    <div class="text-center mt-3">
                        <a href="{{ route('payments.index') }}" class="btn btn-primary btn-sm">View All Payments</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@hasPermission('staff.view')
<!-- Staff Widgets Row -->
<div class="row mt-4">
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="widget-stat card bg-primary">
            <div class="card-body p-4">
                <div class="media ai-icon">
                    <span class="me-3 bgl-primary text-primary">
                        <i class="flaticon-381-user-7"></i>
                    </span>
                    <div class="media-body">
                        <p class="mb-1 text-white fw-bold">Total Staff</p>
                        <h4 class="mb-0 text-white fw-bold">{{ $totalStaff ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="widget-stat card bg-success">
            <div class="card-body p-4">
                <div class="media ai-icon">
                    <span class="me-3 bgl-success text-success">
                        <i class="flaticon-381-check"></i>
                    </span>
                    <div class="media-body">
                        <p class="mb-1 text-white fw-bold">Present Today</p>
                        <h4 class="mb-0 text-white fw-bold">{{ $todayPresent ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="widget-stat card bg-danger">
            <div class="card-body p-4">
                <div class="media ai-icon">
                    <span class="me-3 bgl-danger text-danger">
                        <i class="flaticon-381-close"></i>
                    </span>
                    <div class="media-body">
                        <p class="mb-1 text-white fw-bold">Absent Today</p>
                        <h4 class="mb-0 text-white fw-bold">{{ $todayAbsent ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="widget-stat card bg-info">
            <div class="card-body p-4">
                <div class="media ai-icon">
                    <span class="me-3 bgl-info text-info">
                        <i class="flaticon-381-calendar-1"></i>
                    </span>
                    <div class="media-body">
                        <p class="mb-1 text-white fw-bold">Upcoming Assignments</p>
                        <h4 class="mb-0 text-white fw-bold">{{ $upcomingStaffAssignments->count() ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upcoming Staff Assignments Widget -->
@if(isset($upcomingStaffAssignments) && $upcomingStaffAssignments->count() > 0)
<div class="row mt-4">
    <div class="col-xl-6 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Upcoming Staff Assignments</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Event Date</th>
                                <th>Customer</th>
                                <th>Staff Assigned</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($upcomingStaffAssignments as $order)
                                <tr>
                                    <td>
                                        <small>{{ $order->event_date->format('M d, Y') }}</small><br>
                                        <small class="text-muted">{{ $order->eventTime->name ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('customers.show', $order->customer) }}">
                                            {{ $order->customer->name ?? 'N/A' }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($order->staff->count() > 0)
                                            <span class="badge badge-info light">{{ $order->staff->count() }} staff</span>
                                        @else
                                            <span class="text-muted">No staff</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $order->orderStatus && $order->orderStatus->name === 'confirmed' ? 'success' : 'warning' }} light">
                                            {{ $order->orderStatus->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <a href="{{ route('staff.index') }}" class="btn btn-primary btn-sm">View All Staff</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endhasPermission

<!-- Low Stock Alert -->

<!-- Calendar Widget Row - Full Width -->
<div class="row mt-4">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Schedule</h4>
            </div>
            <div class="card-body">
                <div id="calendar" class="app-fullcalendar1"></div>
            </div>
        </div>
    </div>
</div>

<!-- Upcoming Schedule and Today's Deliveries Row - Side by Side -->
<div class="row mt-4">
    <!-- Upcoming Schedule Widget -->
    <div class="col-xl-6 col-lg-12">
        <div class="widget-heading d-flex justify-content-between align-items-center">
            <h3 class="m-0">Upcoming Schedule</h3>
            <a href="{{ route('orders.index') }}" class="btn btn-primary btn-sm">View all</a>
        </div>
        @if(!empty($upcomingSchedule) && count($upcomingSchedule) > 0)
            @foreach($upcomingSchedule as $schedule)
            <div class="card-schedule">
                <span class="side-label {{ $schedule['color'] }}"></span>
                <div class="up-comming-schedule">
                    <div>
                        {{-- <h4><a href="{{ $schedule['url'] }}">{{ $schedule['title'] }}</a></h4> --}}
                        <div class="mb-sm-0 mb-2">
                            <span>{{ $schedule['customer_name'] }}</span>
                        </div>
                    </div>
                    <div>
                        <svg class="me-1" width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13 2.73499H12.255V2.25C12.255 1.83999 11.92 1.5 11.505 1.5C11.09 1.5 10.755 1.83999 10.755 2.25V2.73499H8.75V2.25C8.75 1.83999 8.41501 1.5 8 1.5C7.58499 1.5 7.25 1.83999 7.25 2.25V2.73499H5.245V2.25C5.245 1.83999 4.91001 1.5 4.495 1.5C4.07999 1.5 3.745 1.83999 3.745 2.25V2.73499H3C1.48498 2.73499 0.25 3.96499 0.25 5.48498V12.75C0.25 14.265 1.48498 15.5 3 15.5H13C14.515 15.5 15.75 14.265 15.75 12.75V5.48498C15.75 3.96499 14.515 2.73499 13 2.73499ZM14.25 6.31H1.75V5.48498C1.75 4.79498 2.31 4.23499 3 4.23499H3.745V4.69C3.745 5.10498 4.07999 5.44 4.495 5.44C4.91001 5.44 5.245 5.10498 5.245 4.69V4.23499H7.25V4.69C7.25 5.10498 7.58499 5.44 8 5.44C8.41501 5.44 8.75 5.10498 8.75 4.69V4.23499H10.755V4.69C10.755 5.10498 11.09 5.44 11.505 5.44C11.92 5.44 12.255 5.10498 12.255 4.69V4.23499H13C13.69 4.23499 14.25 4.79498 14.25 5.48498V6.31Z" fill="#c7c7c7"/>
                        </svg>
                        <span>{{ $schedule['date'] }}</span>
                    </div>
                    <div>
                        <svg class="me-1" width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_1049_649)">
                            <path d="M8 16.25C12.275 16.25 15.75 12.775 15.75 8.5C15.75 4.22501 12.275 0.75 8 0.75C3.72501 0.75 0.25 4.22501 0.25 8.5C0.25 12.775 3.72501 16.25 8 16.25ZM7.25 4.345C7.25 3.92999 7.58499 3.595 8 3.595C8.41501 3.595 8.75 3.92999 8.75 4.345V7.75H10.5C10.915 7.75 11.25 8.08499 11.25 8.5C11.25 8.91501 10.915 9.25 10.5 9.25H8C7.58499 9.25 7.25 8.91501 7.25 8.5V4.345Z" fill="#c7c7c7"/>
                            </g>
                            <defs>
                            <clipPath id="clip0_1049_649">
                            <rect width="16" height="16" fill="white" transform="translate(0 0.5)"/>
                            </clipPath>
                            </defs>
                        </svg>
                        <span>{{ $schedule['time'] }}</span>
                    </div>
                    <div>
                        <a href="{{ $schedule['url'] }}"><i class="las la-angle-right text-secondary"></i></a>
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="card">
                <div class="card-body">
                    <p class="text-center text-muted mb-0">No upcoming events scheduled</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Today's Deliveries Widget -->
    <div class="col-xl-6 col-lg-12">
        <div class="widget-heading d-flex justify-content-between align-items-center">
            <h3 class="m-0">Today's Deliveries</h3>
            <a href="{{ route('orders.index') }}" class="btn btn-primary btn-sm">View all</a>
        </div>
        @if(!empty($todayDeliveriesSchedule) && count($todayDeliveriesSchedule) > 0)
            @foreach($todayDeliveriesSchedule as $delivery)
            <div class="card-schedule">
                <span class="side-label {{ $delivery['color'] }}"></span>
                <div class="up-comming-schedule">
                    <div>
                        <div class="mb-sm-0 mb-2">
                            <span>{{ $delivery['customer_name'] }}</span>
                        </div>
                    </div>
                    <div>
                        <svg class="me-1" width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13 2.73499H12.255V2.25C12.255 1.83999 11.92 1.5 11.505 1.5C11.09 1.5 10.755 1.83999 10.755 2.25V2.73499H8.75V2.25C8.75 1.83999 8.41501 1.5 8 1.5C7.58499 1.5 7.25 1.83999 7.25 2.25V2.73499H5.245V2.25C5.245 1.83999 4.91001 1.5 4.495 1.5C4.07999 1.5 3.745 1.83999 3.745 2.25V2.73499H3C1.48498 2.73499 0.25 3.96499 0.25 5.48498V12.75C0.25 14.265 1.48498 15.5 3 15.5H13C14.515 15.5 15.75 14.265 15.75 12.75V5.48498C15.75 3.96499 14.515 2.73499 13 2.73499ZM14.25 6.31H1.75V5.48498C1.75 4.79498 2.31 4.23499 3 4.23499H3.745V4.69C3.745 5.10498 4.07999 5.44 4.495 5.44C4.91001 5.44 5.245 5.10498 5.245 4.69V4.23499H7.25V4.69C7.25 5.10498 7.58499 5.44 8 5.44C8.41501 5.44 8.75 5.10498 8.75 4.69V4.23499H10.755V4.69C10.755 5.10498 11.09 5.44 11.505 5.44C11.92 5.44 12.255 5.10498 12.255 4.69V4.23499H13C13.69 4.23499 14.25 4.79498 14.25 5.48498V6.31Z" fill="#c7c7c7"/>
                        </svg>
                        <span>{{ $delivery['date'] }}</span>
                    </div>
                    <div>
                        <svg class="me-1" width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_1049_649)">
                            <path d="M8 16.25C12.275 16.25 15.75 12.775 15.75 8.5C15.75 4.22501 12.275 0.75 8 0.75C3.72501 0.75 0.25 4.22501 0.25 8.5C0.25 12.775 3.72501 16.25 8 16.25ZM7.25 4.345C7.25 3.92999 7.58499 3.595 8 3.595C8.41501 3.595 8.75 3.92999 8.75 4.345V7.75H10.5C10.915 7.75 11.25 8.08499 11.25 8.5C11.25 8.91501 10.915 9.25 10.5 9.25H8C7.58499 9.25 7.25 8.91501 7.25 8.5V4.345Z" fill="#c7c7c7"/>
                            </g>
                            <defs>
                            <clipPath id="clip0_1049_649">
                            <rect width="16" height="16" fill="white" transform="translate(0 0.5)"/>
                            </clipPath>
                            </defs>
                        </svg>
                        <span>{{ $delivery['time'] }}</span>
                    </div>
                    <div>
                        <a href="{{ $delivery['url'] }}"><i class="las la-angle-right text-secondary"></i></a>
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="card">
                <div class="card-body">
                    <p class="text-center text-muted mb-0">No deliveries today</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.min.js"></script>
<script>
    // Revenue Trend Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: @json($chartData['revenue_trend']['labels']),
                datasets: [{
                    label: 'Revenue',
                    data: @json($chartData['revenue_trend']['data']),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '₹' + context.parsed.y.toLocaleString('en-IN', {minimumFractionDigits: 2});
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₹' + value.toLocaleString('en-IN');
                            }
                        }
                    }
                }
            }
        });
    }

    // Orders Over Time Chart
    const ordersCtx = document.getElementById('ordersChart');
    if (ordersCtx) {
        new Chart(ordersCtx, {
            type: 'line',
            data: {
                labels: @json($chartData['orders_over_time']['labels']),
                datasets: [{
                    label: 'Confirmed',
                    data: @json($chartData['orders_over_time']['confirmed']),
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    tension: 0.1,
                    fill: true
                }, {
                    label: 'Completed',
                    data: @json($chartData['orders_over_time']['completed']),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // Payment Status Distribution Chart
    const paymentStatusCtx = document.getElementById('paymentStatusChart');
    if (paymentStatusCtx) {
        new Chart(paymentStatusCtx, {
            type: 'doughnut',
            data: {
                labels: @json($chartData['payment_status']['labels']),
                datasets: [{
                    data: @json($chartData['payment_status']['data']),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)',
                        'rgba(255, 159, 64, 0.8)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // FullCalendar Initialization
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        if (calendarEl) {
            const calendar = new FullCalendar.Calendar(calendarEl, {
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                initialView: 'dayGridMonth',
                weekNumbers: true,
                navLinks: true,
                nowIndicator: true,
                selectable: false,
                editable: false,
                events: @json($calendarEvents ?? []),
                eventClick: function(info) {
                    if (info.event.url) {
                        window.location.href = info.event.url;
                    }
                }
            });
            calendar.render();
        }
    });
</script>
@endsection
