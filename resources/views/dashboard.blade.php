@extends('layouts.app')

@section('title', 'Dashboard')

@section('page_content')
<!-- Stat Cards Row -->
<div class="row">
    @if($isAdmin || $isManager)
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="widget-stat card bg-primary">
            <div class="card-body p-4">
                <div class="media ai-icon">
                    <span class="me-3 bgl-primary text-primary">
                        <i class="flaticon-381-user-7"></i>
                    </span>
                    <div class="media-body">
                        <p class="mb-1 text-white fw-bold">Total Orders</p>
                        <h4 class="mb-0 text-white fw-bold">{{ $stats['total_orders'] ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="widget-stat card bg-info">
            <div class="card-body p-4">
                <div class="media ai-icon">
                    <span class="me-3 bgl-info text-info">
                        <i class="flaticon-381-calendar-1"></i>
                    </span>
                    <div class="media-body">
                        <p class="mb-1 text-white fw-bold">{{ $isStaff ? 'Upcoming Assignments' : 'Upcoming Events' }}</p>
                        <h4 class="mb-0 text-white fw-bold">{{ $stats['upcoming_events'] ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($isAdmin || $isManager)
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="widget-stat card bg-warning">
            <div class="card-body p-4">
                <div class="media ai-icon">
                    <span class="me-3 bgl-warning text-warning">
                        <i class="flaticon-381-settings-2"></i>
                    </span>
                    <div class="media-body">
                        <p class="mb-1 text-white fw-bold">Pending Payments</p>
                        <h4 class="mb-0 text-white fw-bold">{{ $stats['pending_payments'] ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="widget-stat card bg-success">
            <div class="card-body p-4">
                <div class="media ai-icon">
                    <span class="me-3 bgl-success text-success">
                        <i class="flaticon-381-diamond"></i>
                    </span>
                    <div class="media-body">
                        <p class="mb-1 text-white fw-bold">Completed Events</p>
                        <h4 class="mb-0 text-white fw-bold">{{ $stats['completed_events'] ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($isStaff)
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="widget-stat card bg-warning">
            <div class="card-body p-4">
                <div class="media ai-icon">
                    <span class="me-3 bgl-warning text-warning">
                        <i class="flaticon-381-calendar-1"></i>
                    </span>
                    <div class="media-body">
                        <p class="mb-1 text-white fw-bold">Today's Tasks</p>
                        <h4 class="mb-0 text-white fw-bold">{{ $stats['today_tasks'] ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@if($isAdmin || $isManager)
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
                        <h4 class="mb-0 text-white fw-bold">{{ $stats['total_customers'] ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($isAdmin)
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="widget-stat card bg-success">
            <div class="card-body p-4">
                <div class="media ai-icon">
                    <span class="me-3 bgl-success text-success">
                        <i class="flaticon-381-heart"></i>
                    </span>
                    <div class="media-body">
                        <p class="mb-1 text-white fw-bold">This Month Revenue</p>
                        <h4 class="mb-0 text-white fw-bold">₹{{ number_format($stats['this_month_revenue'] ?? 0, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
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
@endif

<!-- Charts Row -->
@if(!empty($chartData) && ($isAdmin || $isManager))
<div class="row mt-4">
    @if($isAdmin && isset($chartData['revenue_trend']))
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
    @endif
    @if(isset($chartData['orders_over_time']))
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
    @endif
    @if(isset($chartData['payment_status']))
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
    @endif
</div>
@endif

<!-- Lists & Alerts Row -->
@if($isAdmin || $isManager)
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
                                        <small class="text-muted">{{ $item->inventoryUnit?->short_name ?? 'N/A' }}</small>
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
@endif

@if($isAdmin)
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
@elseif($isManager)
<!-- Upcoming Staff Assignments Widget for Manager -->
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
            </div>
        </div>
    </div>
</div>
@endif
@endif
@endsection

@section('scripts')
@if(!empty($chartData) && ($isAdmin || $isManager))
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.min.js"></script>
<script>
    // Revenue Trend Chart (Admin only)
    @if($isAdmin && isset($chartData['revenue_trend']))
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: @json($chartData['revenue_trend']['labels'] ?? []),
                datasets: [{
                    label: 'Revenue',
                    data: @json($chartData['revenue_trend']['data'] ?? []),
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
    @endif

    // Orders Over Time Chart
    @if(isset($chartData['orders_over_time']))
    const ordersCtx = document.getElementById('ordersChart');
    if (ordersCtx) {
        new Chart(ordersCtx, {
            type: 'line',
            data: {
                labels: @json($chartData['orders_over_time']['labels'] ?? []),
                datasets: [{
                    label: 'Confirmed',
                    data: @json($chartData['orders_over_time']['confirmed'] ?? []),
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    tension: 0.1,
                    fill: true
                }, {
                    label: 'Completed',
                    data: @json($chartData['orders_over_time']['completed'] ?? []),
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
    @endif

    // Payment Status Distribution Chart
    @if(isset($chartData['payment_status']))
    const paymentStatusCtx = document.getElementById('paymentStatusChart');
    if (paymentStatusCtx) {
        new Chart(paymentStatusCtx, {
            type: 'doughnut',
            data: {
                labels: @json($chartData['payment_status']['labels'] ?? []),
                datasets: [{
                    data: @json($chartData['payment_status']['data'] ?? []),
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
    @endif
</script>
@endif
@endsection
