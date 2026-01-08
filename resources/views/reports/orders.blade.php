@extends('layout.default')

@section('title', 'Orders Report')

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">Reports</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Orders</a></li>
        </ol>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Orders Report</h4>
                </div>
                <div class="card-body">
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" name="start_date" value="{{ $startDate }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">End Date</label>
                                    <input type="date" name="end_date" value="{{ $endDate }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">&nbsp;</label>
                                    <a href="{{ route('reports.export', ['type' => 'orders', 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success btn-block">
                                        <i class="fa fa-download me-2"></i>Export Excel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Summary Cards -->
                    <div class="row">
                        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-2">
                            <div class="widget-stat card bg-primary">
                                <div class="card-body p-4">
                                    <div class="media ai-icon">
                                        <span class="me-3 bgl-primary text-primary">
                                            <i class="flaticon-381-user-7"></i>
                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1">Total Orders</p>
                                            <h4 class="mb-0">{{ $summary['total_orders'] }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
                            <div class="widget-stat card bg-success">
                                <div class="card-body p-4">
                                    <div class="media ai-icon">
                                        <span class="me-3 bgl-success text-success">
                                            <i class="flaticon-381-heart"></i>
                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1">Total Amount</p>
                                            <h4 class="mb-0">₹{{ number_format($summary['total_amount'], 2) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
                            <div class="widget-stat card bg-warning">
                                <div class="card-body p-4">
                                    <div class="media ai-icon">
                                        <span class="me-3 bgl-warning text-warning">
                                            <i class="flaticon-381-calendar-1"></i>
                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1">Confirmed</p>
                                            <h4 class="mb-0">{{ $summary['confirmed'] }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
                            <div class="widget-stat card bg-info">
                                <div class="card-body p-4">
                                    <div class="media ai-icon">
                                        <span class="me-3 bgl-info text-info">
                                            <i class="flaticon-381-diamond"></i>
                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1">Completed</p>
                                            <h4 class="mb-0">{{ $summary['completed'] }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
                            <div class="widget-stat card bg-danger">
                                <div class="card-body p-4">
                                    <div class="media ai-icon">
                                        <span class="me-3 bgl-danger text-danger">
                                            <i class="flaticon-381-settings-2"></i>
                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1">Pending</p>
                                            <h4 class="mb-0">{{ $summary['pending'] }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Section -->
                    <div class="row mt-4">
                        <div class="col-xl-6 col-lg-12 col-sm-12">
                            <div class="card">
                                <div class="card-header border-0 pb-0">
                                    <h4 class="card-title">Order Trends</h4>
                                </div>
                                <div class="card-body">
                                    <div id="orderTrendsChart"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-12 col-sm-12">
                            <div class="card">
                                <div class="card-header border-0 pb-0">
                                    <h4 class="card-title">Order Status Distribution</h4>
                                </div>
                                <div class="card-body">
                                    <canvas id="orderStatusChart" height="100"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Orders by Event Type Chart -->
                    @if(isset($chartData['event_types']) && count($chartData['event_types']['labels']) > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header border-0 pb-0">
                                    <h4 class="card-title">Orders by Event Type</h4>
                                </div>
                                <div class="card-body">
                                    <div id="eventTypeChart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Orders Table -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header border-0 pb-0">
                                    <h4 class="card-title">Order Details</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-responsive-md">
                                            <thead>
                                                <tr>
                                                    <th><strong>Order #</strong></th>
                                                    <th><strong>Customer</strong></th>
                                                    <th><strong>Event Date</strong></th>
                                                    <th><strong>Amount</strong></th>
                                                    <th><strong>Status</strong></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($orders as $group)
                                                    @php
                                                        $status = $group['status'];
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            {{ $group['order_number'] }}
                                                            @if($group['orders']->count() > 1)
                                                                <span class="badge light badge-info">({{ $group['orders']->count() }} orders)</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $group['customer']->name }}</td>
                                                        <td>{{ $group['event_date']->format('M d, Y') }}</td>
                                                        <td>₹{{ number_format($group['total_amount'], 2) }}</td>
                                                        <td>
                                                            <span class="badge light 
                                                                {{ $status === 'confirmed' ? 'badge-success' : '' }}
                                                                {{ $status === 'pending' ? 'badge-warning' : '' }}
                                                                {{ $status === 'completed' ? 'badge-primary' : '' }}
                                                                {{ $status === 'cancelled' ? 'badge-danger' : '' }}
                                                                {{ $status === 'mixed' ? 'badge-info' : '' }}">
                                                                {{ ucfirst($status) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center">No orders found</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function($) {
    "use strict";
    
    $(document).ready(function() {
        const chartData = @json($chartData ?? []);
        const isDarkMode = $('body').hasClass('dark-mode') || $('html').hasClass('dark');

        // Chart.js defaults
        if (typeof Chart !== 'undefined') {
            Chart.defaults.color = isDarkMode ? '#9CA3AF' : '#6B7280';
            Chart.defaults.borderColor = isDarkMode ? '#374151' : '#E5E7EB';
            Chart.defaults.defaultFontFamily = 'Poppins';
        }

        // Order Trends Chart (ApexCharts Line Chart)
        if ($('#orderTrendsChart').length > 0 && chartData.trends) {
            var trendsOptions = {
                series: [{
                    name: 'Orders',
                    data: chartData.trends.data
                }],
                chart: {
                    type: 'line',
                    height: 350,
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    }
                },
                colors: ['var(--primary)'],
                stroke: {
                    width: 3,
                    curve: 'smooth',
                    lineCap: 'round'
                },
                markers: {
                    size: 6,
                    strokeWidth: 3,
                    strokeColors: '#fff',
                    hover: {
                        size: 8
                    }
                },
                dataLabels: {
                    enabled: false
                },
                grid: {
                    borderColor: isDarkMode ? '#374151' : '#E5E7EB',
                    xaxis: {
                        lines: {
                            show: true
                        }
                    },
                    yaxis: {
                        lines: {
                            show: false
                        }
                    }
                },
                xaxis: {
                    categories: chartData.trends.labels,
                    labels: {
                        style: {
                            colors: isDarkMode ? '#9CA3AF' : '#6B7280',
                            fontSize: '14px',
                            fontFamily: 'Poppins'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: isDarkMode ? '#9CA3AF' : '#6B7280',
                            fontSize: '14px',
                            fontFamily: 'Poppins'
                        }
                    },
                    forceNiceScale: true
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        type: 'vertical',
                        shadeIntensity: 0.3,
                        gradientToColors: ['var(--primary)'],
                        inverseColors: false,
                        opacityFrom: 0.4,
                        opacityTo: 0.1,
                        stops: [0, 100]
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return value + ' orders';
                        }
                    }
                }
            };

            var trendsChart = new ApexCharts(document.querySelector("#orderTrendsChart"), trendsOptions);
            trendsChart.render();
        }

        // Order Status Distribution Chart (Chart.js Doughnut Chart)
        if ($('#orderStatusChart').length > 0 && chartData.status) {
            const statusCtx = document.getElementById('orderStatusChart').getContext('2d');
            
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: chartData.status.labels,
                    datasets: [{
                        data: chartData.status.data,
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(239, 68, 68, 0.8)',
                            'rgba(139, 92, 246, 0.8)',
                        ],
                        borderColor: [
                            'rgb(59, 130, 246)',
                            'rgb(16, 185, 129)',
                            'rgb(245, 158, 11)',
                            'rgb(239, 68, 68)',
                            'rgb(139, 92, 246)',
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return label + ': ' + value + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }

        // Orders by Event Type Chart (ApexCharts Bar Chart)
        if ($('#eventTypeChart').length > 0 && chartData.event_types && chartData.event_types.labels.length > 0) {
            var eventTypeOptions = {
                series: [{
                    name: 'Orders',
                    data: chartData.event_types.data
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: {
                        show: false
                    }
                },
                colors: ['var(--primary)'],
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded',
                        borderRadius: 4
                    }
                },
                dataLabels: {
                    enabled: false
                },
                grid: {
                    borderColor: isDarkMode ? '#374151' : '#E5E7EB',
                    xaxis: {
                        lines: {
                            show: true
                        }
                    },
                    yaxis: {
                        lines: {
                            show: false
                        }
                    }
                },
                xaxis: {
                    categories: chartData.event_types.labels,
                    labels: {
                        style: {
                            colors: isDarkMode ? '#9CA3AF' : '#6B7280',
                            fontSize: '14px',
                            fontFamily: 'Poppins'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: isDarkMode ? '#9CA3AF' : '#6B7280',
                            fontSize: '14px',
                            fontFamily: 'Poppins'
                        }
                    },
                    forceNiceScale: true
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return value + ' orders';
                        }
                    }
                }
            };

            var eventTypeChart = new ApexCharts(document.querySelector("#eventTypeChart"), eventTypeOptions);
            eventTypeChart.render();
        }
    });
})(jQuery);
</script>
@endsection
