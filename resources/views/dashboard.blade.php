@extends('layouts.app')

@section('title', 'Dashboard')

@section('page_content')
<div class="row">
    <!-- Stats Cards -->
    <div class="col-xl-3 col-6">
        <div class="card overflow-hidden">
            <div class="card-body p-0">
                <div class="px-4 py-3 bg-primary" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="row items-center">
                        <div class="col">
                            <span class="text-white text-sm font-medium">Total Orders</span>
                            <h3 class="text-white font-bold">{{ $stats['total_orders'] }}</h3>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-file-text text-white" style="font-size: 2rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-6">
        <div class="card overflow-hidden">
            <div class="card-body p-0">
                <div class="px-4 py-3 bg-success" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <div class="row items-center">
                        <div class="col">
                            <span class="text-white text-sm font-medium">Upcoming Events</span>
                            <h3 class="text-white font-bold">{{ $stats['upcoming_events'] }}</h3>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar text-white" style="font-size: 2rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-6">
        <div class="card overflow-hidden">
            <div class="card-body p-0">
                <div class="px-4 py-3 bg-warning" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <div class="row items-center">
                        <div class="col">
                            <span class="text-white text-sm font-medium">Pending Payments</span>
                            <h3 class="text-white font-bold">{{ $stats['pending_payments'] }}</h3>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-credit-card text-white" style="font-size: 2rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-6">
        <div class="card overflow-hidden">
            <div class="card-body p-0">
                <div class="px-4 py-3 bg-info" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                    <div class="row items-center">
                        <div class="col">
                            <span class="text-white text-sm font-medium">Completed Events</span>
                            <h3 class="text-white font-bold">{{ $stats['completed_events'] }}</h3>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle text-white" style="font-size: 2rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alerts & Notifications -->
@if($lowStockItems > 0 || $pendingPayments->count() > 0)
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Alerts & Notifications</h4>
            </div>
            <div class="card-body">
                @if($lowStockItems > 0)
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>{{ $lowStockItems }} items are low on stock</strong> - Action required
                        <a href="{{ route('inventory.low-stock') }}" class="btn btn-warning btn-sm ms-3">View</a>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if($pendingPayments->count() > 0)
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>{{ $pendingPayments->count() }} orders have pending payments</strong> - Follow up needed
                        <a href="{{ route('orders.index') }}?payment_status=pending" class="btn btn-danger btn-sm ms-3">View</a>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<!-- Charts Section -->
<div class="row">
    <div class="col-xl-6 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Revenue Trend (Last 6 Months)</h4>
            </div>
            <div class="card-body">
                <canvas id="revenueTrendChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Orders Over Time</h4>
            </div>
            <div class="card-body">
                <canvas id="ordersOverTimeChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-6 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Payment Status Distribution</h4>
            </div>
            <div class="card-body">
                <canvas id="paymentStatusChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Monthly Revenue Comparison</h4>
            </div>
            <div class="card-body">
                <canvas id="monthlyComparisonChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-6 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Upcoming Events</h4>
            </div>
            <div class="card-body">
                @if($upcomingEvents->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order Number</th>
                                    <th>Customer</th>
                                    <th>Event Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($upcomingEvents as $event)
                                    <tr>
                                        <td>{{ $event->order_number }}</td>
                                        <td>{{ $event->customer->name }}</td>
                                        <td>{{ $event->event_date->format('M d, Y') }}</td>
                                        <td><a href="{{ route('orders.show', $event) }}" class="btn btn-primary btn-sm">View</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-muted py-4">No upcoming events</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Today's Deliveries</h4>
            </div>
            <div class="card-body">
                @if($todayDeliveries->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order Number</th>
                                    <th>Customer</th>
                                    <th>Event Time</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($todayDeliveries as $delivery)
                                    <tr>
                                        <td>{{ $delivery->order_number }}</td>
                                        <td>{{ $delivery->customer->name }}</td>
                                        <td>{{ ucfirst($delivery->event_time) }}</td>
                                        <td><a href="{{ route('orders.show', $delivery) }}" class="btn btn-success btn-sm">View</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-muted py-4">No deliveries scheduled for today</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/chart.js/Chart.bundle.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartData = @json($chartData);

    // Revenue Trend Chart (Line Chart)
    const revenueCtx = document.getElementById('revenueTrendChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: chartData.revenue_trend.labels,
                datasets: [{
                    label: 'Revenue (Payments Received) (₹)',
                    data: chartData.revenue_trend.data,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '₹' + parseFloat(context.parsed.y).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
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

    // Orders Over Time Chart (Bar Chart)
    const ordersCtx = document.getElementById('ordersOverTimeChart');
    if (ordersCtx) {
        new Chart(ordersCtx, {
            type: 'bar',
            data: {
                labels: chartData.orders_over_time.labels,
                datasets: [
                    {
                        label: 'Confirmed',
                        data: chartData.orders_over_time.confirmed,
                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                        borderColor: 'rgb(16, 185, 129)',
                        borderWidth: 1
                    },
                    {
                        label: 'Completed',
                        data: chartData.orders_over_time.completed,
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
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

    // Payment Status Distribution (Doughnut Chart)
    const paymentStatusCtx = document.getElementById('paymentStatusChart');
    if (paymentStatusCtx) {
        new Chart(paymentStatusCtx, {
            type: 'doughnut',
            data: {
                labels: chartData.payment_status.labels,
                datasets: [{
                    data: chartData.payment_status.data,
                    backgroundColor: [
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                    ],
                    borderColor: [
                        'rgb(239, 68, 68)',
                        'rgb(245, 158, 11)',
                        'rgb(16, 185, 129)',
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    }

    // Monthly Revenue Comparison (Bar Chart)
    const monthlyCtx = document.getElementById('monthlyComparisonChart');
    if (monthlyCtx) {
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: chartData.monthly_comparison.labels,
                datasets: [{
                    label: 'Revenue (₹)',
                    data: [chartData.monthly_comparison.previous, chartData.monthly_comparison.current],
                    backgroundColor: [
                        'rgba(156, 163, 175, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                    ],
                    borderColor: [
                        'rgb(156, 163, 175)',
                        'rgb(59, 130, 246)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '₹' + parseFloat(context.parsed.y).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
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
});
</script>
@endpush

