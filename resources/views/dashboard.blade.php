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
                        <h4 class="mb-0 text-white fw-bold">{{ $lowStockItems }}</h4>
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
    <!-- Upcoming Events -->
    <div class="col-xl-4 col-lg-6 col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Upcoming Events</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($upcomingEvents as $event)
                                <tr>
                                    <td>
                                        <a href="{{ route('customers.show', $event->customer) }}">
                                            {{ $event->customer->name ?? 'N/A' }}
                                        </a>
                                    </td>
                                    <td>
                                        <small>{{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}</small><br>
                                        <small class="text-muted">{{ $event->event_time ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $event->status === 'confirmed' ? 'success' : 'warning' }} light">
                                            {{ ucfirst($event->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-3 text-muted">No upcoming events</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($upcomingEvents->count() > 0)
                    <div class="text-center mt-3">
                        <a href="{{ route('orders.index') }}" class="btn btn-primary btn-sm">View All Orders</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Today's Deliveries -->
    <div class="col-xl-4 col-lg-6 col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Today's Deliveries</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($todayDeliveries as $delivery)
                                <tr>
                                    <td>
                                        <a href="{{ route('customers.show', $delivery->customer) }}">
                                            {{ $delivery->customer->name ?? 'N/A' }}
                                        </a>
                                    </td>
                                    <td>
                                        <small>{{ $delivery->event_time ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $delivery->status === 'confirmed' ? 'success' : 'warning' }} light">
                                            {{ ucfirst($delivery->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-3 text-muted">No deliveries today</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($todayDeliveries->count() > 0)
                    <div class="text-center mt-3">
                        <a href="{{ route('orders.index') }}" class="btn btn-primary btn-sm">View All Orders</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Pending Payments -->
    <div class="col-xl-4 col-lg-6 col-md-12">
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
@if($lowStockItems > 0)
<div class="row mt-4">
    <div class="col-12">
        <x-alert type="warning" title="Warning!">
            You have {{ $lowStockItems }} {{ Str::plural('item', $lowStockItems) }} with low stock. 
            <a href="{{ route('inventory.low-stock') }}" class="alert-link">View low stock items</a> to restock.
        </x-alert>
    </div>
</div>
@endif
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
</script>
@endsection
